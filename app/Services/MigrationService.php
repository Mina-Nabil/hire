<?php


namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Base\City;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Location;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MigrationService
{
    public static function downloadTemplate()
    {
        return response()->download(resource_path('sheets/HireStartup.xlsx'));
    }

    public static function migrateFromStartupfile($file, &$locations, &$departments, &$employees, &$salary_grades, &$positions)
    {
        $template = IOFactory::load($file);
        if (!$template) {
            throw new AppException('Failed to read template file');
        }
        try {
            DB::transaction(function () use ($template, &$locations, &$departments, &$employees, &$salary_grades, &$positions) {

                //load locations
                $locations_sheet = $template->getSheet(0);
                $highestRow = $locations_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $locationName = $locations_sheet->getCell('A' . $row)->getValue();
                    $locations[] = [
                        'name' => $locationName,
                        'warning' => !$locationName,
                    ];
                }

                //load departments
                $departments_sheet = $template->getSheet(1);
                $highestRow = $departments_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $departmentCode = $departments_sheet->getCell('A' . $row)->getValue();
                    $departmentName = $departments_sheet->getCell('B' . $row)->getValue();
                    $departmentDescription = $departments_sheet->getCell('C' . $row)->getValue();

                    $departments[] = [
                        'code' => $departmentCode,
                        'name' => $departmentName,
                        'description' => $departmentDescription,
                        'warning' => !$departmentCode || !$departmentName,
                    ];
                }

                //load employees
                $employees_sheet = $template->getSheet(3);
                $highestRow = $employees_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $employeeName = $employees_sheet->getCell('A' . $row)->getValue();
                    $employeeNameAr = $employees_sheet->getCell('B' . $row)->getValue();
                    $id_number = $employees_sheet->getCell('C' . $row)->getValue();
                    $employeeEmail = $employees_sheet->getCell('D' . $row)->getValue();
                    $employeePhone = $employees_sheet->getCell('E' . $row)->getValue();
                    $employeeAddress = $employees_sheet->getCell('F' . $row)->getValue();
                    $nationality = $employees_sheet->getCell('G' . $row)->getValue();
                    $gender = $employees_sheet->getCell('H' . $row)->getValue();
                    $birthDate = $employees_sheet->getCell('I' . $row)->getValue();
                    if (!$employeeName) continue;
                    $city_name = $employees_sheet->getCell('J' . $row)->getValue();
                    $city = City::where('name', $city_name)->first();
                    if (!$city) {
                        throw new AppException('City not found on row ' . $row);
                    }
                    $employment_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employees_sheet->getCell('K' . $row)->getValue());

                    // Extract first and last name from full name
                    $nameParts = explode(' ', $employeeName);
                    $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
                    $lastName = isset($nameParts[count($nameParts) - 1]) ? $nameParts[count($nameParts) - 1] : '';

                    // Convert to lowercase and remove non-alphanumeric characters
                    $firstName = preg_replace('/[^a-z0-9]/', '', strtolower($firstName));
                    $lastName = preg_replace('/[^a-z0-9]/', '', strtolower($lastName));

                    // Generate base username
                    $baseUsername = $firstName . $lastName;

                    // Ensure username is not empty
                    if (empty($baseUsername)) {
                        $baseUsername = 'employee' . rand(100, 999);
                    }

                    $employees[] = [
                        'name' => $employeeName,
                        'name_ar' => $employeeNameAr,
                        'email' => $employeeEmail,
                        'phone' => $employeePhone,
                        'address' => $employeeAddress,
                        'nationality' => $nationality,
                        'gender' => $gender,
                        'birth_date' => $birthDate,
                        'id_number' => $id_number,
                        'employment_date' => $employment_date,
                        'city_id' => $city->id,
                        'city_name' => $city_name,

                        'base_username' => $baseUsername,
                        'base_password' => "pass@123",
                        'type' => User::TYPE_EMPLOYEE,
                        'warning' => !$employeeName || !$employeeNameAr || !$id_number || !$employeeEmail || !$employeePhone || !$employeeAddress || !$nationality || !$gender || !$birthDate || !$city_name,
                    ];


                    // $tmpUser = User::createUser($employeeName, $baseUsername, "pass@123", User::TYPE_EMPLOYEE);

                    // Employee::createEmployee(
                    //     $tmpUser->id,
                    //     $employeeName,
                    //     $employeeNameAr,
                    //     $employeeEmail,
                    //     $employeePhone,
                    //     $employeeAddress,
                    //     $nationality,
                    //     $gender,
                    //     $birthDate,
                    //     $id_number,
                    //     false,
                    //     $employment_date,
                    //     $city->id
                    // );
                }

                //load salary grades
                $salary_grades_sheet = $template->getSheet(4);
                $highestRow = $salary_grades_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $salaryGradeName = $salary_grades_sheet->getCell('A' . $row)->getValue();
                    if ($salaryGradeName == 'LEAVE A ROW EMPTY LIKE THIS ONE BETWEEN EACH GRADE LEVEL' || !$salaryGradeName) continue;
                    $salaryGradeGrossMin = $salary_grades_sheet->getCell('C' . $row)->getValue();
                    $salaryGradeGrossMax = $salary_grades_sheet->getCell('D' . $row)->getValue();
                    $extraBenefits = [];
                    $k = ++$row;
                    $benefits_row = $salary_grades_sheet->getCell('B' . $k)->getValue();
                    while ($benefits_row != 'LEAVE A ROW EMPTY LIKE THIS ONE BETWEEN EACH GRADE LEVEL' && $benefits_row != null) {
                        $name = $benefits_row;
                        $min = $salary_grades_sheet->getCell('C' . $k)->getValue();
                        $max = $salary_grades_sheet->getCell('D' . $k)->getValue();
                        $to = $salary_grades_sheet->getCell('E' . $k)->getValue();
                        $type = $salary_grades_sheet->getCell('F' . $k)->getValue();


                        $extraBenefits[] = [
                            'name' => $name,
                            'min' => $min,
                            'max' => $max,
                            'to' => $to,
                            'type' => $type,
                            'warning' => !$name || !$min || !$max || !$to || !$type,
                        ];
                        $k++;
                        $benefits_row = $salary_grades_sheet->getCell('B' . $k)->getValue();
                    }
                    if ($k > $row + 1)
                        $row = $k;

                    $salary_grades[] = [
                        'name' => $salaryGradeName,
                        'gross_min' => $salaryGradeGrossMin,
                        'gross_max' => $salaryGradeGrossMax,
                        'extra_benefits' => $extraBenefits,
                        'warning' => !$salaryGradeName || !$salaryGradeGrossMin || !$salaryGradeGrossMax,
                    ];

                    // SalaryGrade::createSalaryGrade($salaryGradeName, $salaryGradeGrossMin, $salaryGradeGrossMax);
                }

                //load positions
                $positions_sheet = $template->getSheet(2);
                $highestRow = $positions_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {

                    $locationName = $positions_sheet->getCell('A' . $row)->getValue();
                    $location = Location::where('name', $locationName)->first();
                    $departmentName = $positions_sheet->getCell('B' . $row)->getValue();
                    $department = Department::where('name', $departmentName)->first();
                    $positionName = $positions_sheet->getCell('C' . $row)->getValue();
                    $positionArabicName = $positions_sheet->getCell('D' . $row)->getValue();
                    $positionCode = $positions_sheet->getCell('E' . $row)->getValue();
                    $parentPositionCode = $positions_sheet->getCell('F' . $row)->getValue();
                    $parentPosition = Position::where('code', $parentPositionCode)->first();
                    $employeeIDNumber = $positions_sheet->getCell('G' . $row)->getValue();
                    $employee = Employee::where('id_number', $employeeIDNumber)->first();
                    $salaryGradeName = $positions_sheet->getCell('H' . $row)->getValue();
                    $salaryGrade = SalaryGrade::where('name', $salaryGradeName)->first();

                    if (!$departmentName || !$positionName || !$positionArabicName) continue;

                    if (!$department || !$location) {
                        throw new AppException('Department or Location not found on row ' . $row);
                    }

                    $positions[] = [
                        'location_id' => $location->id,
                        'department_id' => $department->id,
                        'name' => $positionName,
                        'arabic_name' => $positionArabicName,
                        'parent_id' => $parentPosition->id ?? null,
                        'code' => $positionCode,
                        'employee_id' => $employee->id ?? null,
                        'salary_grade_id' => $salaryGrade->id ?? null,
                        'warning' => !$location || !$department || !$positionName || !$positionArabicName,
                    ];
                    // $position = Position::createPosition(
                    //     locationId: $location->id,
                    //     departmentId: $department->id,
                    //     name: $positionName,
                    //     arabicName: $positionArabicName,
                    //     parentId: $parentPosition->id ?? null,
                    //     code: $positionCode,
                    //     employeeId: $employee->id ?? null,
                    //     salaryGradeId: $salaryGrade->id ?? null,
                    // );
                }
            });
        } catch (Exception $e) {
            throw new AppException('Failed to migrate: ' . $e->getMessage());
        }
    }
}
