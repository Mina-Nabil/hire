<?php


namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Base\City;
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

    public static function migrateFromStartupfile($file)
    {
        $template = IOFactory::load($file);
        if (!$template) {
            throw new AppException('Failed to read template file');
        }
        try {
            DB::transaction(function () use ($template) {
                $locations_sheet = $template->getSheet(0);
                $highestRow = $locations_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $locationName = $locations_sheet->getCell('A' . $row)->getValue();
                    if (!$locationName) continue;

                    Location::createLocation($locationName);
                }

                $departments_sheet = $template->getSheet(1);
                $highestRow = $departments_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $departmentCode = $departments_sheet->getCell('A' . $row)->getValue();
                    $departmentName = $departments_sheet->getCell('B' . $row)->getValue();
                    $departmentDescription = $departments_sheet->getCell('C' . $row)->getValue();
                    if (!$departmentCode || !$departmentName) continue;

                    Department::createDepartment($departmentCode, $departmentName, $departmentDescription);
                }

                $employees_sheet = $template->getSheet(3);
                $highestRow = $employees_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $employeeName = $employees_sheet->getCell('A' . $row)->getValue();
                    $employeeEmail = $employees_sheet->getCell('B' . $row)->getValue();
                    $employeePhone = $employees_sheet->getCell('C' . $row)->getValue();
                    $employeeAddress = $employees_sheet->getCell('D' . $row)->getValue();
                    $nationality = $employees_sheet->getCell('E' . $row)->getValue();
                    $gender = $employees_sheet->getCell('F' . $row)->getValue();
                    $birthDate = $employees_sheet->getCell('G' . $row)->getValue();
                    if (!$employeeName) continue;
                    $city_name = $employees_sheet->getCell('H' . $row)->getValue();
                    $city = City::where('name', $city_name)->first();
                    if (!$city) {
                        throw new AppException('City not found on row ' . $row);
                    }
                    $employment_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employees_sheet->getCell('I' . $row)->getValue());

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

                    $tmpUser = User::createUser($employeeName, $baseUsername, "pass@123", User::TYPE_EMPLOYEE);

                    Employee::createEmployee(
                        $tmpUser->id,
                        $employeeName,
                        $employeeEmail,
                        $employeePhone,
                        $employeeAddress,
                        $nationality,
                        $gender,
                        $birthDate,
                        $city->id,
                        false,
                        $employment_date
                    );
                }

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
                    $parentPositionName = $positions_sheet->getCell('F' . $row)->getValue();
                    $parentPosition = Position::where('name', $parentPositionName)->first();

                    if (!$departmentName || !$positionName || !$positionArabicName) continue;

                    if (!$department || !$location) {
                        throw new AppException('Department or Location not found on row ' . $row);
                    }

                    $position = Position::createPosition(
                        locationId: $location->id,
                        departmentId: $department->id,
                        name: $positionName,
                        arabicName: $positionArabicName,
                        parentId: $parentPosition->id ?? null,
                        code: $positionCode,
                    );
                }
            });
        } catch (Exception $e) {
            throw new AppException('Failed to migrate: ' . $e->getMessage());
        }
    }
}
