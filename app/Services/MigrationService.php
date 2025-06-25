<?php


namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Base\City;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Location;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Employee;
use App\Models\Personel\EmployeeInfo;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
                    $locationName = $locations_sheet->getCell('A' . $row)->getValueString();
                    if (!$locationName) continue;
                    $locations[] = [
                        'name' => trim($locationName),
                        'not_valid' => !$locationName,
                    ];
                }

                //load departments
                $departments_sheet = $template->getSheet(1);
                $highestRow = $departments_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $departmentCode = $departments_sheet->getCell('A' . $row)->getValueString();
                    $departmentName = $departments_sheet->getCell('B' . $row)->getValueString();
                    $departmentDescription = $departments_sheet->getCell('C' . $row)->getValueString();
                    if (!$departmentName) continue;
                    $departments[] = [
                        'code' => trim($departmentCode),
                        'name' => trim($departmentName),
                        'description' => $departmentDescription,
                        'not_valid' => !$departmentCode || !$departmentName,
                    ];
                }

                //load employees
                $employees_sheet = $template->getSheet(3);
                $highestRow = $employees_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $employeeName = $employees_sheet->getCell('A' . $row)->getValueString();
                    $employeeNameAr = $employees_sheet->getCell('B' . $row)->getValueString();
                    $id_number = $employees_sheet->getCell('C' . $row)->getValueString();
                    $employeeEmail = $employees_sheet->getCell('D' . $row)->getValueString();
                    $employeePhone = $employees_sheet->getCell('E' . $row)->getValueString();
                    $employeeAddress = $employees_sheet->getCell('F' . $row)->getValueString();
                    $nationality = $employees_sheet->getCell('G' . $row)->getValueString();
                    $gender = $employees_sheet->getCell('H' . $row)->getValueString();
                
                    try {
                        if ($employees_sheet->getCell('I' . $row)->getValue()) {
                            $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employees_sheet->getCell('I' . $row)->getValue());
                        } else {
                            $birthDate = Carbon::parse('1990-01-01');
                        }
                    } catch (Exception $e) {
                        $birthDate = Carbon::parse('1990-01-01');
                    }
                    if (!$employeeName) continue;
                    $city_name = $employees_sheet->getCell('J' . $row)->getValueString();
                    $city = City::where('name', $city_name)->first();
                    if (!$city) {
                        throw new AppException('Emploees Sheet: City name is not valid on row ' . $row);
                    }

                    try {
                        if ($employees_sheet->getCell('K' . $row)->getValue()) {
                            $employment_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employees_sheet->getCell('K' . $row)->getValue());
                        } else {
                            $employment_date = Carbon::parse('2024-01-01');
                        }
                    } catch (Exception $e) {
                        $employment_date = Carbon::parse('2024-01-01');
                    }
                    $employeeCode = $employees_sheet->getCell('L' . $row)->getValueString();

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

                    // Ensure the username is not take
                    $i = 1;
                    while (User::where('username', $baseUsername)->exists()) {
                        $baseUsername = $baseUsername . $i;
                        $i++;
                    }

                    $invalid_reason = match (true) {
                        !$employeeName => 'Employee name is required',
                        !$employeeNameAr => 'Employee name arabic is required',
                        !$id_number => 'ID number is required',
                        !$employeeEmail => 'Employee email is required',
                        !$employeePhone => 'Employee phone is required',
                        !$employeeAddress => 'Employee address is required',
                        !$nationality => 'Nationality is required',
                        !$gender => 'Gender is required',
                        !$birthDate => 'Birth date is required',
                        !$city_name => 'City name is required',
                        !$employment_date => 'Employment date is required',
                        true => null,
                    };


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
                        'employee_code' => $employeeCode,

                        'base_username' => $baseUsername,
                        'base_password' => "pass@123",
                        'type' => User::TYPE_EMPLOYEE,
                        'not_valid' => $invalid_reason ? true : false,
                        'invalid_reason' => $invalid_reason,
                    ];
                }

                //load salary grades
                $salary_grades_sheet = $template->getSheet(4);
                $highestRow = $salary_grades_sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $salaryGradeName = trim($salary_grades_sheet->getCell('A' . $row)->getValueString());
                    if ($salaryGradeName == 'LEAVE A ROW EMPTY LIKE THIS ONE BETWEEN EACH GRADE LEVEL' || !$salaryGradeName) continue;
                    $salaryGradeGrossMin = $salary_grades_sheet->getCell('C' . $row)->getValueString();
                    $salaryGradeGrossMax = $salary_grades_sheet->getCell('D' . $row)->getValueString();
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
                            'not_valid' => !$name || !$max || !$to || !$type,
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

                    $locationName = $positions_sheet->getCell('A' . $row)->getValueString();
                    $departmentName = trim($positions_sheet->getCell('B' . $row)->getValueString());
                    $positionName = trim($positions_sheet->getCell('C' . $row)->getValueString());
                    $positionArabicName = trim($positions_sheet->getCell('D' . $row)->getValueString());
                    $positionCode = trim($positions_sheet->getCell('E' . $row)->getValueString());
                    $parentPositionCode = trim($positions_sheet->getCell('F' . $row)->getValueString());
                    $employeeIDNumber = trim($positions_sheet->getCell('G' . $row)->getValueString());
                    $salaryGradeName = trim($positions_sheet->getCell('H' . $row)->getValueString());

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

                    if ($employeeIDNumber && !array_filter($employees, fn($employee) => $employee['id_number'] == $employeeIDNumber)) {
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
                        'employee_id' => $positionCode,
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
                    $employee_code = $employee['employee_code'];
                    if (!$employee['not_valid']) {
                        $user = User::create([
                            'name' => $employee['name'],
                            'username' => $employee['base_username'],
                            'password' => $employee['base_password'],
                            'type' => User::TYPE_EMPLOYEE,
                        ]);

                        $employee = Employee::create([
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
                            'license_required' => false, // Default value
                            'status' => Employee::STATUS_ACTIVE, // Default status
                            'created_by' => Auth::id() ?? 1, // Current user or default admin
                        ]);

                        $employee->info()->create([
                            'employee_code' => $employee_code,
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
                                    'receiver' => $eb['to'],
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
                            $position_employee = Employee::whereHas('info', function ($query) use ($position) {
                                $query->where('employee_code', $position['employee_id']);
                            })->first();
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
                            'employee_id' => isset($position_employee) ? $position_employee->id : null,
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

    /**
     * Load employee data from Excel file and validate it
     * Stage 1: Load and validate data, determine if employee is new/updated/has errors
     * 
     * @param string $file Path to the Excel file
     * @return array Array containing validated employee data with status and errors
     * @throws AppException
     */
    public static function LoadEmployeeData($file): array
    {
        $results = [
            'total_rows' => 0,
            'new_employees' => [],
            'updated_employees' => [],
            'errors' => [],
            'summary' => [
                'new_count' => 0,
                'update_count' => 0,
                'error_count' => 0
            ]
        ];

        try {
            $spreadsheet = IOFactory::load($file);
            if (!$spreadsheet) {
                throw new AppException('Failed to read Excel file');
            }

            $activeSheet = $spreadsheet->getActiveSheet();
            $highestRow = $activeSheet->getHighestRow();
            
            // Skip header row, start from row 3 (assuming row 1 is headers, row 2 might be empty)
            for ($row = 3; $row <= $highestRow; $row++) {
                $results['total_rows']++;

                // Extract data from Excel columns (based on the export format)
                $employeeData = [
                    'row_number' => $row,
                    'id' => $activeSheet->getCell('A' . $row)->getValue(),
                    'name' => trim($activeSheet->getCell('B' . $row)->getValueString()),
                    'name_ar' => trim($activeSheet->getCell('C' . $row)->getValueString()),
                    'email' => trim($activeSheet->getCell('D' . $row)->getValueString()),
                    'phone' => trim($activeSheet->getCell('E' . $row)->getValueString()),
                    'address' => trim($activeSheet->getCell('F' . $row)->getValueString()),
                    'nationality' => trim($activeSheet->getCell('G' . $row)->getValueString()),
                    'gender' => trim($activeSheet->getCell('H' . $row)->getValueString()),
                    'birth_date' => $activeSheet->getCell('I' . $row)->getValue(),
                    'employment_date' => $activeSheet->getCell('J' . $row)->getValue(),
                    'id_number' => trim($activeSheet->getCell('K' . $row)->getValueString()),
                    'mother_name' => trim($activeSheet->getCell('L' . $row)->getValueString()),
                    'birth_place_name' => trim($activeSheet->getCell('M' . $row)->getValueString()),
                    'insurance_office_name' => trim($activeSheet->getCell('N' . $row)->getValueString()),
                    'insurance_number' => trim($activeSheet->getCell('O' . $row)->getValueString()),
                    'academic_qualification' => trim($activeSheet->getCell('P' . $row)->getValueString()),
                    'university' => trim($activeSheet->getCell('Q' . $row)->getValueString()),
                    'graduation_year' => trim($activeSheet->getCell('R' . $row)->getValueString()),
                    'military_status' => trim($activeSheet->getCell('S' . $row)->getValueString()),
                    'marital_status' => trim($activeSheet->getCell('T' . $row)->getValueString()),
                    'employee_code' => trim($activeSheet->getCell('U' . $row)->getValueString()),
                    'device_id' => trim($activeSheet->getCell('V' . $row)->getValueString()),
                ];

                // Skip empty rows
                if (empty($employeeData['name']) && empty($employeeData['email'])) {
                    $results['total_rows']--;
                    continue;
                }

                // Validate and process the employee data
                $processedData = self::validateAndProcessEmployeeData($employeeData);

                // Categorize the employee based on validation results
                if (!empty($processedData['errors'])) {
                    $results['errors'][] = $processedData;
                    $results['summary']['error_count']++;
                } elseif ($processedData['is_new']) {
                    $results['new_employees'][] = $processedData;
                    $results['summary']['new_count']++;
                } else {
                    $results['updated_employees'][] = $processedData;
                    $results['summary']['update_count']++;
                }
            }

            AppLog::info('Employee data loaded successfully', [
                'total_rows' => $results['total_rows'],
                'new_employees' => $results['summary']['new_count'],
                'updated_employees' => $results['summary']['update_count'],
                'errors' => $results['summary']['error_count']
            ]);

            return $results;

        } catch (Exception $e) {
            AppLog::error('Failed to load employee data', $e->getMessage());
            throw new AppException('Failed to load employee data: ' . $e->getMessage());
        }
    }

    /**
     * Validate and process individual employee data
     * 
     * @param array $employeeData Raw employee data from Excel
     * @return array Processed employee data with validation results
     */
    private static function validateAndProcessEmployeeData(array $employeeData): array
    {
        $errors = [];
        $warnings = [];
        $processedData = $employeeData;

        // Required field validation
        $requiredFields = [
            'name' => 'Employee name is required',
            'name_ar' => 'Employee Arabic name is required',
            'email' => 'Email is required',
            'phone' => 'Phone number is required',
            'address' => 'Address is required',
            'nationality' => 'Nationality is required',
            'gender' => 'Gender is required',
            'id_number' => 'ID number is required'
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($employeeData[$field])) {
                $errors[] = $message;
            }
        }

        // Gender validation
        if (!empty($employeeData['gender']) && !in_array(strtolower($employeeData['gender']), ['male', 'female'])) {
            $errors[] = 'Gender must be either Male or Female';
        }

        // Date validation and formatting
        try {
            if (!empty($employeeData['birth_date'])) {
                if (is_numeric($employeeData['birth_date'])) {
                    // Excel date format
                    $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employeeData['birth_date']);
                    $processedData['birth_date'] = $birthDate->format('Y-m-d');
                } else {
                    // Try to parse as string
                    $birthDate = Carbon::parse($employeeData['birth_date']);
                    $processedData['birth_date'] = $birthDate->format('Y-m-d');
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Invalid birth date format';
        }

        try {
            if (!empty($employeeData['employment_date'])) {
                if (is_numeric($employeeData['employment_date'])) {
                    // Excel date format
                    $employmentDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employeeData['employment_date']);
                    $processedData['employment_date'] = $employmentDate->format('Y-m-d');
                } else {
                    // Try to parse as string
                    $employmentDate = Carbon::parse($employeeData['employment_date']);
                    $processedData['employment_date'] = $employmentDate->format('Y-m-d');
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Invalid employment date format';
        }

        // Check if birth place exists
        if (!empty($employeeData['birth_place_name'])) {
            $birthPlace = City::where('name', $employeeData['birth_place_name'])->first();
            if (!$birthPlace) {
                $errors[] = 'Birth place "' . $employeeData['birth_place_name'] . '" not found in system';
            } else {
                $processedData['birth_place_id'] = $birthPlace->id;
            }
        }

        // Check if insurance office exists (if provided)
        if (!empty($employeeData['insurance_office_name'])) {
            $insuranceOffice = \App\Models\Base\InsuranceOffice::where('name', $employeeData['insurance_office_name'])->first();
            if (!$insuranceOffice) {
                $warnings[] = 'Insurance office "' . $employeeData['insurance_office_name'] . '" not found in system';
            } else {
                $processedData['insurance_office_id'] = $insuranceOffice->id;
            }
        }

        // Determine if this is a new employee or update
        $existingEmployee = null;
        $isNew = true;

        // First try to find by ID (if provided and not empty)
        if (!empty($employeeData['id']) && is_numeric($employeeData['id'])) {
            $existingEmployee = Employee::find($employeeData['id']);
            if ($existingEmployee) {
                $isNew = false;
            }
        }

        // If not found by email, try by ID number
        if (!$existingEmployee && !empty($employeeData['id_number'])) {
            $existingEmployee = Employee::where('id_number', $employeeData['id_number'])->first();
            if ($existingEmployee) {
                $isNew = false;
                $processedData['id'] = $existingEmployee->id;
                $warnings[] = 'Employee found by ID number';
            }
        }

        // If not found by ID number, try by employee code
        if (!$existingEmployee && !empty($employeeData['employee_code'])) {
            $existingEmployee = Employee::whereHas('info', function($query) use ($employeeData) {
                $query->where('employee_code', $employeeData['employee_code']);
            })->first();
            if ($existingEmployee) {
                $isNew = false;
                $processedData['id'] = $existingEmployee->id;
                $warnings[] = 'Employee found by employee code, but other details may differ';
            }
        }

        // Check for duplicate email/ID number if this is a new employee
        if ($isNew) {
            if (!empty($employeeData['id_number'])) {
                $idExists = Employee::where('id_number', $employeeData['id_number'])->exists();
                if ($idExists) {
                    $errors[] = 'ID number already exists in system';
                }
            }
        }

        $processedData['is_new'] = $isNew;
        $processedData['existing_employee'] = $existingEmployee;
        $processedData['errors'] = $errors;
        $processedData['warnings'] = $warnings;

        return $processedData;
    }

    /**
     * Save valid employee data to the database
     * Stage 2: Import new employees and update existing ones
     * 
     * @param array $employeeData The validated employee data from LoadEmployeeData
     * @return array Results of the save operation
     * @throws AppException
     */
    public static function SaveEmployeeData(array $employeeData): array
    {
        $results = [
            'created_count' => 0,
            'updated_count' => 0,
            'created_employees' => [],
            'updated_employees' => [],
            'errors' => []
        ];

        try {
            DB::transaction(function () use ($employeeData, &$results) {
                
                // Process new employees
                foreach ($employeeData['new_employees'] as $newEmployeeData) {
                    try {
                        $results['created_employees'][] = self::createNewEmployee($newEmployeeData);
                        $results['created_count']++;
                    } catch (Exception $e) {
                        $results['errors'][] = [
                            'row' => $newEmployeeData['row_number'],
                            'type' => 'create_error',
                            'message' => 'Failed to create employee: ' . $e->getMessage(),
                            'data' => $newEmployeeData
                        ];
                    }
                }

                // Process employee updates
                foreach ($employeeData['updated_employees'] as $updateEmployeeData) {
                    try {
                        $results['updated_employees'][] = self::updateExistingEmployee($updateEmployeeData);
                        $results['updated_count']++;
                    } catch (Exception $e) {
                        $results['errors'][] = [
                            'row' => $updateEmployeeData['row_number'],
                            'type' => 'update_error',
                            'message' => 'Failed to update employee: ' . $e->getMessage(),
                            'data' => $updateEmployeeData
                        ];
                    }
                }
            });

            AppLog::info('Employee data saved successfully', [
                'created_count' => $results['created_count'],
                'updated_count' => $results['updated_count'],
                'errors_count' => count($results['errors'])
            ]);

            return $results;

        } catch (Exception $e) {
            AppLog::error('Failed to save employee data', $e->getMessage());
            throw new AppException('Failed to save employee data: ' . $e->getMessage());
        }
    }

    /**
     * Create a new employee from Excel data
     * 
     * @param array $employeeData Validated employee data
     * @return Employee The created employee
     * @throws Exception
     */
    private static function createNewEmployee(array $employeeData): Employee
    {
        // Generate username from name
        $nameParts = explode(' ', $employeeData['name']);
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

        // Ensure the username is unique
        $username = $baseUsername;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }

        // Create user account
        $user = User::create([
            'name' => $employeeData['name'],
            'username' => $username,
            'password' => 'pass@123', // Default password
            'type' => User::TYPE_EMPLOYEE,
        ]);

        // Create employee record
        $employee = Employee::create([
            'user_id' => $user->id,
            'name' => $employeeData['name'],
            'name_ar' => $employeeData['name_ar'],
            'email' => $employeeData['email'],
            'phone' => $employeeData['phone'],
            'address' => $employeeData['address'],
            'nationality' => $employeeData['nationality'],
            'gender' => $employeeData['gender'],
            'birth_date' => $employeeData['birth_date'],
            'employment_date' => $employeeData['employment_date'],
            'id_number' => $employeeData['id_number'],
            'mother_name' => $employeeData['mother_name'],
            'birth_place_id' => $employeeData['birth_place_id'] ?? null,
            'license_required' => false, // Default value
            'status' => Employee::STATUS_ACTIVE, // Default status
            'created_by' => Auth::id() ?? 1, // Current user or default admin
        ]);

        // Create employee info record
        $employee->info()->create([
            'insurance_office_id' => $employeeData['insurance_office_id'] ?? null,
            'insurance_number' => $employeeData['insurance_number'],
            'academic_qualification' => $employeeData['academic_qualification'],
            'university' => $employeeData['university'],
            'graduation_year' => $employeeData['graduation_year'],
            'military_status' => $employeeData['military_status'],
            'marital_status' => $employeeData['marital_status'],
            'employee_code' => $employeeData['employee_code'],
            'device_id' => $employeeData['device_id'] ?? null,
        ]);

        AppLog::info('New employee created from import', [
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'row_number' => $employeeData['row_number']
        ]);

        return $employee;
    }

    /**
     * Update an existing employee from Excel data
     * 
     * @param array $employeeData Validated employee data
     * @return Employee The updated employee
     * @throws Exception
     */
    private static function updateExistingEmployee(array $employeeData): Employee
    {
        $employee = Employee::find($employeeData['id']);
        if (!$employee) {
            throw new Exception('Employee not found for update');
        }

        // Update base employee information
        $employee->updateBaseInfo(
            $employeeData['name'],
            $employeeData['name_ar'],
            $employeeData['email'],
            $employeeData['phone'],
            $employeeData['address'],
            $employeeData['nationality'],
            $employeeData['gender'],
            $employeeData['birth_date'],
            $employeeData['employment_date'],
            $employeeData['id_number'],
            $employeeData['mother_name']
        );

        // Update employee info
        $employee->updateEmployeeInfo(
            $employeeData['insurance_office_id'] ?? null,
            $employeeData['insurance_number'],
            $employeeData['academic_qualification'],
            $employeeData['university'],
            $employeeData['graduation_year'],
            $employeeData['military_status'],
            $employeeData['marital_status'],
            $employeeData['employee_code'],
            $employeeData['device_id']
        );

        AppLog::info('Employee updated from import', [
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'row_number' => $employeeData['row_number']
        ]);

        return $employee;
    }
}
