<?php


namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Base\City;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Location;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
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
        $locations = [];
        $departments = [];
        $employees = [];
        $salary_grades = [];
        $positions = [];

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
                    if (!$locationName) continue;
                    $locations[] = [
                        'name' => $locationName,
                        'not_valid' => !$locationName,
                    ];
                }

                //load departments
                $departments_sheet = $template->getSheet(1);
                $highestRow = $departments_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $departmentCode = $departments_sheet->getCell('A' . $row)->getValue();
                    $departmentName = $departments_sheet->getCell('B' . $row)->getValue();
                    $departmentDescription = $departments_sheet->getCell('C' . $row)->getValue();
                    if (!$departmentName) continue;
                    $departments[] = [
                        'code' => $departmentCode,
                        'name' => $departmentName,
                        'description' => $departmentDescription,
                        'not_valid' => !$departmentCode || !$departmentName,
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
                        throw new AppException('Emploees Sheet: City name is not valid on row ' . $row);
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
                        'birth_date' => Carbon::parse($birthDate)->format('Y-m-d'),
                        'id_number' => $id_number,
                        'employment_date' => Carbon::parse($employment_date)->format('Y-m-d'),
                        'city_id' => $city->id,
                        'city_name' => $city_name,

                        'base_username' => $baseUsername,
                        'base_password' => "pass@123",
                        'type' => User::TYPE_EMPLOYEE,
                        'not_valid' => !$employeeName || !$employeeNameAr || !$id_number || !$employeeEmail || !$employeePhone || !$employeeAddress || !$nationality || !$gender || !$birthDate || !$city_name,
                    ];
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
                            'not_valid' => !$name || !$min || !$max || !$to || !$type,
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
                        'not_valid' => !$salaryGradeName || !$salaryGradeGrossMin || !$salaryGradeGrossMax,
                    ];
                }

                //load positions
                $positions_sheet = $template->getSheet(2);
                $highestRow = $positions_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {

                    $locationName = $positions_sheet->getCell('A' . $row)->getValue();
                    $departmentName = $positions_sheet->getCell('B' . $row)->getValue();
                    $positionName = $positions_sheet->getCell('C' . $row)->getValue();
                    $positionArabicName = $positions_sheet->getCell('D' . $row)->getValue();
                    $positionCode = $positions_sheet->getCell('E' . $row)->getValue();
                    $parentPositionCode = $positions_sheet->getCell('F' . $row)->getValue();
                    $employeeIDNumber = $positions_sheet->getCell('G' . $row)->getValue();
                    $salaryGradeName = $positions_sheet->getCell('H' . $row)->getValue();

                    if (!$departmentName || !$positionName || !$positionArabicName) continue;
                    $invalid = false;
                    $invalid_reason = null;

                    if (!array_filter($departments, fn($department) => $department['name'] == $departmentName) || !array_filter($locations, fn($location) => $location['name'] == $locationName)) {
                        $invalid = true;
                        $invalid_reason = 'Department or Location not found';
                    }

                    if (!array_filter($salary_grades, fn($salary_grade) => $salary_grade['name'] == $salaryGradeName)) {
                        $invalid = true;
                        $invalid_reason = 'Salary Grade not found';
                    }

                    if (!array_filter($employees, fn($employee) => $employee['id_number'] == $employeeIDNumber)) {
                        $invalid = true;
                        $invalid_reason = 'Employee not found';
                    }

                    $positions[] = [
                        'location_id' => $locationName,
                        'department_id' => $departmentName,
                        'name' => $positionName,
                        'arabic_name' => $positionArabicName,
                        'parent' => $parentPositionCode,
                        'code' => $positionCode,
                        'employee_id' => $employeeIDNumber,
                        'salary_grade' => $salaryGradeName,
                        'not_valid' => $invalid,
                        'invalid_reason' => $invalid_reason,
                    ];
                }
            });
        } catch (Exception $e) {
            throw new AppException('Failed to migrate: ' . $e->getMessage());
        }
    }

    public static function importData($locations, $departments, $employees, $salary_grades, $positions)
    {
        try {

            DB::transaction(function () use ($locations, $departments, $employees, $salary_grades, $positions) {

                foreach ($locations as $location) {
                    if (!$location['not_valid']) {
                        Location::create([
                            'name' => $location['name'],
                        ]);
                    }
                }

                foreach ($departments as $department) {
                    if (!$department['not_valid']) {
                        Department::create([
                            'name' => $department['name'],
                            'prefix_code' => $department['code'],
                            'description' => $department['description'],
                        ]);
                    }
                }

                foreach ($employees as $employee) {
                    if (!$employee['not_valid']) {
                        $user = User::create([
                            'name' => $employee['name'],
                            'username' => $employee['base_username'],
                            'password' => $employee['base_password'],
                            'type' => User::TYPE_EMPLOYEE,
                        ]);

                        Employee::create([
                            'user_id' => $user->id,
                            'name' => $employee['name'],
                            'name_ar' => $employee['name_ar'],
                            'email' => $employee['email'],
                            'phone' => $employee['phone'],
                            'address' => $employee['address'],
                            'nationality' => $employee['nationality'],
                            'gender' => $employee['gender'],
                            'birth_date' => Carbon::parse($employee['birth_date'])->format('Y-m-d'),
                            'id_number' => $employee['id_number'],
                            'employment_date' => Carbon::parse($employee['employment_date'])->format('Y-m-d'),
                            'birth_place_id' => $employee['city_id'],
                            'created_by' => 1,
                        ]);
                    }
                }

                foreach ($salary_grades as $salary_grade) {
                    if (!$salary_grade['not_valid']) {
                        $extra_benefits = [];
                        foreach ($salary_grade['extra_benefits'] as $eb) {
                            if (!$eb['not_valid']) {
                                $extra_benefits[] = [
                                    'name' => $eb['name'],
                                    'amount_min' => $eb['min'],
                                    'amount_max' => $eb['max'],
                                    'amount_to' => $eb['to'],
                                    'type' => $eb['type'],
                                ];
                            }
                        }

                        $salary_grade = SalaryGrade::create([
                            'name' => $salary_grade['name'],
                            'gross_min' => $salary_grade['gross_min'],
                            'gross_max' => $salary_grade['gross_max'],
                        ]);

                        $salary_grade->packageDetails()->createMany($extra_benefits);
                    }
                }

                foreach ($positions as $position) {
                    if (!$position['not_valid']) {
                        $location = Location::where('name', $position['location_id'])->first();
                        $department = Department::where('name', $position['department_id'])->first();
                        if ($position['salary_grade']) {
                            $salary_grade = SalaryGrade::where('name', $position['salary_grade'])->first();
                        }
                        if ($position['employee_id']) {
                            $employee = Employee::where('id_number', $position['employee_id'])->first();
                        }
                        if ($position['parent']) {
                            $parent_position = Position::where('code', $position['parent'])->first();
                        }

                        Position::create([
                            'location_id' => $location->id,
                            'department_id' => $department->id,
                            'name' => $position['name'],
                            'arabic_name' => $position['arabic_name'],
                            'parent_id' => isset($parent_position) ? $parent_position->id : null,
                            'code' => $position['code'],
                            'employee_id' => isset($employee) ? $employee->id : null,
                            'salary_grade_id' => isset($salary_grade) ? $salary_grade->id : null,
                        ]);
                    }
                }
            });
            AppLog::info('Data imported successfully');
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to import data', $e->getMessage());
            throw new AppException('Failed to import data');
        }
    }
}
