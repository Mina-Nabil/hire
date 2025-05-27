<?php

namespace App\Models\Personel;

use App\Exceptions\AppException;
use App\Models\Attendance\Overtime;
use App\Models\Attendance\PublicHoliday;
use App\Models\Base\City;
use App\Models\Base\InsuranceOffice;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Extras\Loan;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Extras\Purchase;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\VacationPackage;
use App\Models\Benefits\Configurations\WorkingDay;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Docs\ArmyServicePaper;
use App\Models\Personel\Docs\BankAccount;
use App\Models\Personel\Docs\BirthCertificate;
use App\Models\Personel\Docs\DriverLicense;
use App\Models\Personel\Docs\EmployeeContract;
use App\Models\Personel\Docs\EmployeeS1Doc;
use App\Models\Personel\Docs\EmployeeS2Doc;
use App\Models\Personel\Docs\EmployeeS6Doc;
use App\Models\Personel\Docs\ExternalMedicalRecord;
use App\Models\Personel\Docs\HrLetter;
use App\Models\Personel\Docs\IDCard;
use App\Models\Personel\Docs\MedicalRecord;
use App\Models\Personel\Docs\PoliceRecord;
use App\Models\Personel\Docs\PracticeCard;
use App\Models\Personel\Docs\SkillsQualification;
use App\Models\Personel\Docs\SyndicateCard;
use App\Models\Personel\Docs\WorkDeclaration;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Personel\Docs\EmployeeHrLetterRequest;
use App\Models\Users\AppLog;

class Employee extends Model
{
    const MORPH_NAME = 'employee';

    const FILES_DIRECTORY = 'employees';

    // Document status constants
    const DOC_STATUS_VALID = 'valid';
    const DOC_STATUS_NEAR_EXPIRY = 'near_expiry';
    const DOC_STATUS_EXPIRED = 'expired';
    const DOC_STATUS_MISSING = 'missing';

    // Default days threshold for near expiry warning (30 days)
    const NEAR_EXPIRY_DAYS = 30;

    // Employee status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_RESIGNED = 'resigned';

    const STATUS_LIST = [
        self::STATUS_ACTIVE,
        self::STATUS_SUSPENDED,
        self::STATUS_TERMINATED,
        self::STATUS_RESIGNED,
    ];

    // Employee statuses
    protected $fillable = [
        'user_id',
        'created_by',
        'id_number',
        'name',
        'name_ar',
        'email',
        'phone',
        'address',
        'nationality',
        'gender',
        'birth_date',
        'image_url',
        'birth_place_id',
        'license_required',
        'employment_date',
        'applicant_id',
        'termination_date',
        'mother_name',
        'status',
    ];

    protected $casts = [
        'employment_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date'
    ];

    protected static function booted()
    {
        static::addGlobalScope('hrAccessibleEmployees', function ($builder) {
            $user = Auth::user();

            // If no user is logged in or if they are admin, don't restrict
            if (!$user || $user->is_admin) {
                return;
            }

            // If user is HR, restrict to employees in their assigned locations
            if ($user->is_hr) {
                // Get the HR user's assigned location IDs
                $locationIds = $user->assignedLocations()->pluck('locations.id')->toArray();

                // Only apply filter if the user has assigned locations
                if (!empty($locationIds)) {
                    $builder->whereHas('positions', function ($query) use ($locationIds) {
                        $query->whereIn('location_id', $locationIds);
                    });
                }
            }
        });
    }

    ////attributes
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getFullImageUrlAttribute(): string|null
    {
        if (!$this->image_url) {
            return null;
        }

        // Use string concatenation since the url method isn't available
        $baseUrl = config('filesystems.disks.s3.url', 'https://s3.amazonaws.com');
        $bucket = config('filesystems.disks.s3.bucket');
        return rtrim($baseUrl, '/') . '/' . $bucket . '/' . ltrim($this->image_url, '/');
    }


    ////model benefit functions
    /**
     * Apply for benefit package
     * @param SalaryGrade $salaryGrade
     * @param array $package_details
     * [
     *  [
     *      'package_detail_id' => 1,
     *      'amount' => 1000,
     *      'receiver' => 'employee',
     *      'type' => 'monthly',
     *      'start_date' => '2025-01-01',
     *      'end_date' => '2025-12-31' //optional
     *  ],
     * ]
     * @param float $grossSalary
     * @param int $manager_id
     * @param bool $delete_old_conf delete old configuration if true and end old configuration if false
     * @return void
     */
    public function applyBenefitsPackage(SalaryGrade $salaryGrade, $package_details, $grossSalary, $insuranceAmount, $manager_id = null, bool $delete_old_conf = true)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this)) {
            throw new AppException('You dont have permission to apply benefits package');
        }

        if ($grossSalary < $salaryGrade->gross_min || $grossSalary > $salaryGrade->gross_max) {
            throw new AppException("Gross salary is not in the range of the salary grade");
        }


        foreach ($package_details as $applied_package_detail) {
            $package_detail = PackageDetail::find($applied_package_detail['package_detail_id']);
            if (!($applied_package_detail['amount'] >= $package_detail->amount_min
                && $applied_package_detail['amount'] <= $package_detail->amount_max)) {
                throw new AppException('Amount is not in the range of the package detail');
            }
            $applied_package_detail['type'] = $package_detail->type;
        }

        try {
            DB::transaction(function () use ($package_details, $salaryGrade, $grossSalary, $manager_id, $delete_old_conf, $insuranceAmount) {
                if ($delete_old_conf) {
                    $this->baseBenefits()->delete();
                } else {
                    $start_date = Carbon::parse($this->baseBenefits()->first()->start_date);
                    $this->baseBenefits()->update([
                        'end_date' => $start_date->subDay()->format('Y-m-d'),
                    ]);
                }
                $this->baseBenefits()->createMany($package_details);
                $this->benefitConfiguration()->updateOrCreate([
                    'employee_id' => $this->id,
                ], [
                    'salary_grade_id' => $salaryGrade->id,
                    'vacation_package_id' => $salaryGrade->vacation_package_id,
                    'gross_salary' => $grossSalary,
                    'insurance_amount' => $insuranceAmount,
                    'manager_id' => $manager_id,
                ]);
                AppLog::info('Benefits Package Applied', 'Benefits package applied for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error applying benefits package', $e->getMessage(), loggable: $this);
            throw new AppException('Error applying benefits package');
        }
    }

    /**
     * Apply for benefit package
     * @param VacationPackage $salaryGrade
     * @param array $vacation_benefits
     * [
     *  [
     *      'vacation_detail_id' => 1,
     *      'start_date' => '2025-01-01',
     *      'end_date' => '2025-12-31' //optional
     *      'inc_rate' => 100,
     *      'max_balance' => 100,
     *      'hour_price' => 100,
     *  ],
     * ]
     * @return void
     */
    public function applyVacationPackage(VacationPackage $vacationPackage, $vacation_benefits, bool $delete_old_conf = true)
    {
        foreach ($vacation_benefits as $applied_vacation_benefit) {
            $vacation_benefit = VacationDetail::find($applied_vacation_benefit['vacation_detail_id']);
            if (!($applied_vacation_benefit['inc_rate'] >= $vacation_benefit->inc_rate_min
                && $applied_vacation_benefit['inc_rate'] <= $vacation_benefit->inc_rate_max)) {
                throw new AppException('Increase rate is not in the range of the ' . $vacation_benefit->name);
            }
            if (!($applied_vacation_benefit['max_balance'] >= $vacation_benefit->max_balance_min
                && $applied_vacation_benefit['max_balance'] <= $vacation_benefit->max_balance_max)) {
                throw new AppException('Maximum balance is not in the range of the ' . $vacation_benefit->name);
            }
            if (!($applied_vacation_benefit['hour_price'] >= $vacation_benefit->hour_price_min
                && $applied_vacation_benefit['hour_price'] <= $vacation_benefit->hour_price_max)) {
                throw new AppException('Hour price is not in the range of the ' . $vacation_benefit->name);
            }
            $applied_vacation_benefit['type'] = $vacation_benefit->type;
        }

        try {
            DB::transaction(function () use ($vacation_benefits, $vacationPackage, $delete_old_conf) {
                if ($delete_old_conf) {
                    $this->vacationBenefits()->delete();
                } else {
                    $start_date = Carbon::parse($this->vacationBenefits()->first()->start_date);
                    $this->vacationBenefits()->update([
                        'end_date' => $start_date->subDay()->format('Y-m-d'),
                    ]);
                }
                $this->vacationBenefits()->createMany($vacation_benefits);
                $this->benefitConfiguration()->updateOrCreate([
                    'employee_id' => $this->id,
                ], [
                    'vacation_package_id' => $vacationPackage->id,
                ]);
                AppLog::info('Vacation Package Applied', 'Vacation package applied for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error applying vacation package', $e->getMessage(), loggable: $this);
            throw new AppException('Error applying benefits package');
        }
    }

    /**
     * Apply for benefit package
     * @param array $working_days
     * ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday']
     * @param int $attendace_calculation
     * @param int $working_day_start_min
     * @param int $working_day_start_max
     * @param int $working_day_end_min
     * @param int $working_day_end_max
     * @param int $daily_working_hours
     * @param int $overtime_rate
     * @param bool $is_automatic_overtime
     * @return void
     */
    public function setAttendanceConfigurations(array $working_days, $attendace_calculation, $working_day_start_min, $working_day_start_max, $working_day_end_min, $working_day_end_max, $daily_working_hours, $is_automatic_overtime, $overtime_rate, $is_require_attendance_approval = false)
    {
        if ($attendace_calculation == BenefitConfiguration::ATTENDANCE_CALCULATION_FIXED) {
            if ($working_day_start_min !== $working_day_start_max) {
                throw new AppException('Working day start min and max must be the same for fixed attendance calculation');
            }
            if ($working_day_end_min !== $working_day_end_max) {
                throw new AppException('Working day end min and max must be the same for fixed attendance calculation');
            }
        }

        if ($attendace_calculation == BenefitConfiguration::ATTENDANCE_CALCULATION_SEMI_FLEXIBLE) {
            if ($working_day_start_min == $working_day_start_max) {
                throw new AppException('Working day start min and max must be different for semi-flexible attendance calculation');
            }
            if ($working_day_end_min == $working_day_end_max) {
                throw new AppException('Working day end min and max must be different for semi-flexible attendance calculation');
            }
        }

        $min_duration_diff = Carbon::parse($working_day_start_min)->diffInHours($working_day_end_min);
        $max_duration_diff = Carbon::parse($working_day_start_max)->diffInHours($working_day_end_max);

        if ($min_duration_diff != $max_duration_diff) {
            throw new AppException('Working day date range is not valid, must be the same between min and max');
        }

        if ($min_duration_diff != $daily_working_hours) {
            throw new AppException('Working day duration is not valid, must be the same as the daily working hours difference');
        }

        try {
            DB::transaction(function () use ($attendace_calculation, $working_day_start_min, $working_day_start_max, $working_day_end_min, $working_day_end_max, $daily_working_hours, $overtime_rate, $is_automatic_overtime, $working_days, $is_require_attendance_approval) {
                $this->workingDays()->delete();

                $dbWorkingDays = [];
                foreach ($working_days as $working_day) {
                    $dbWorkingDays[] = [
                        'type' => $working_day,
                    ];
                }

                $this->workingDays()->createMany($dbWorkingDays);
                $this->benefitConfiguration()->updateOrCreate([
                    'employee_id' => $this->id,
                ], [
                    'is_automatic_overtime' => $is_automatic_overtime,
                    'is_require_attendance_approval' => $is_require_attendance_approval,
                    'attendace_calculation' => $attendace_calculation,
                    'working_day_start_min' => $working_day_start_min,
                    'working_day_start_max' => $working_day_start_max,
                    'working_day_end_min' => $working_day_end_min,
                    'working_day_end_max' => $working_day_end_max,
                    'daily_working_hours' => $daily_working_hours,
                    'overtime_rate' => $overtime_rate,
                ]);
                AppLog::info('Attendance Configurations Set', 'Attendance configurations set for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting attendance configurations', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting attendance configurations');
        }
    }


    /**
     * Set a base benefit either from a package or a custom one
     * @param float $amount
     * @param string $type
     * @param Carbon $start_date
     * @param Carbon|null $end_date
     * @param int|null $package_detail_id - if null, the benefit is custom
     * @param string|null $name - if not null, the benefit is custom
     */
    public function addCustomBaseBenefit(string $name, float $amount, string $type, string $receiver, Carbon $start_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this)) {
            throw new AppException('You dont have permission to set base benefit');
        }
        try {
            DB::transaction(function () use ($name, $amount, $type, $start_date, $receiver) {
                $this->baseBenefits()->create([
                    'name' => $name,
                    'amount' => $amount,
                    'type' => $type,
                    'start_date' => $start_date,
                    'receiver' => $receiver,
                ]);
                AppLog::info('Custom Base Benefit Added', 'Custom base benefit added for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error adding custom base benefit', $e->getMessage(), loggable: $this);
            throw new AppException('Error adding custom base benefit');
        }
    }

    /**
     * Add a custom vacation benefit
     * @param string $name
     * @param float $inc_rate
     * @param float $hour_price
     * @param float $max_balance
     * @param Carbon $start_date
     * @param Carbon|null $end_date
     * @return void
     */
    public function addCustomVacationBenefit(
        string $name,
        float $inc_rate,
        float $hour_price,
        float $current_balance,
        float $max_balance,
        string $type,
        Carbon $start_date
    ) {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this)) {
            throw new AppException('You dont have permission to set vacation benefit');
        }
        try {

            $this->vacationBenefits()->create([
                'name' => $name,
                'inc_rate' => $inc_rate,
                'hour_price' => $hour_price,
                'current_balance' => $current_balance,
                'max_balance' => $max_balance,
                'type' => $type,
                'start_date' => $start_date
            ]);
            AppLog::info('Custom Vacation Benefit Added', 'Custom vacation benefit added for employee: ' . $this->name, loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error adding custom vacation benefit', $e->getMessage(), loggable: $this);
            throw new AppException('Error adding custom vacation benefit');
        }
    }

    public function addOvertime(Carbon $start_date, Carbon $end_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('addOvertime', $this)) {
            throw new AppException('You dont have permission to add overtime');
        }
        $day = $start_date->copy()->startOfDay();
        $hours = $start_date->diffInHours($end_date);
        try {
            $this->overtimes()->create([
                'date' => $day,
                'start_time' => $start_date->format('H:i'),
                'end_time' => $end_date->format('H:i'),
                'hours' => $hours,
                'status' => 'pending',
                'creator_id' => $loggedInUser->id,
            ]);
            AppLog::info('Overtime Added', 'Overtime added for employee: ' . $this->name, loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error adding overtime', $e->getMessage(), loggable: $this);
            throw new AppException('Error adding overtime');
        }
    }

    /**
     * Apply for vacation
     * @param VacationBenefit $vacationBenefit
     * @param float $hours_count
     * @param array $days
     * [
     *  [
     *      'vacation_date' => '2025-01-01',
     *      'hours' => 8,
     *  ],
     *  [
     *      'vacation_date' => '2025-01-02',
     *      'hours' => 8,
     *  ],
     * ]
     * @return void
     */
    public function applyForVacation(VacationBenefit $vacationBenefit, float $hours_count, array $days = [], bool $is_approved = false)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('applyForVacation', $this)) {
            throw new AppException('You dont have permission to apply for vacation');
        }

        $currentBalance = $vacationBenefit->current_balance;

        if ($currentBalance < $hours_count) {
            throw new AppException('You dont have enough vacation days');
        }
        try {
            DB::transaction(function () use ($hours_count, $days, $currentBalance, $vacationBenefit, $is_approved) {
                $appliedVacation = $this->appliedVacations()->create([
                    'vacation_benefit_id' => $vacationBenefit->id,
                    'hours' => $hours_count,
                    'new_balance' => $currentBalance - $hours_count,
                    'status' => $is_approved ? AppliedVacation::STATUS_APPROVED : AppliedVacation::STATUS_PENDING,
                ]);
                // dd($days);
                if (count($days) > 0) {
                    $appliedVacation->vacationDays()->createMany($days);
                }
                $vacationBenefit->update([
                    'current_balance' => $currentBalance - $hours_count,
                ]);
                AppLog::info('Vacation Applied', 'Vacation applied for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error applying for vacation', $e->getMessage(), loggable: $this);
            throw new AppException('Error applying for vacation');
        }
    }

    public function createHrLetterRequest(
        int $requested_by,
        string $directed_to,
        ?string $employee_note = null
    ): EmployeeHrLetterRequest {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('createHrLetterRequest', $this)) {
            throw new AppException('You dont have permission to create HR letter request');
        }

        try {
            return DB::transaction(function () use ($requested_by, $directed_to, $employee_note) {
                return $this->hrLetterRequests()->create([
                    'requested_by' => $requested_by,
                    'directed_to' => $directed_to,
                    'employee_note' => $employee_note,
                    'status' => EmployeeHrLetterRequest::STATUS_PENDING
                ]);
                AppLog::info('HR Letter Request Created', 'HR letter request created for employee: ' . $this->name, loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating HR letter request', $e->getMessage(), loggable: $this);
            throw new AppException('Error creating HR letter request: ' . $e->getMessage());
        }
    }

    ////model document functions
    public function setArmyServicePaper($file_path, Carbon $issue_date, $type = ArmyServicePaper::TYPE_ORIGINAL, ?Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->armyServicePaper()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    'type' => $type,
                ],
            );
            AppLog::info('Army Service Paper Set', 'Army service paper set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting army service paper', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting army service paper');
        }
    }

    public function setBirthCertificate($file_path, Carbon $issue_date, $type = BirthCertificate::TYPE_ORIGINAL, ?Carbon $expiry_date = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->birthCertificate()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    'type' => $type,
                ],
            );
            AppLog::info('Birth Certificate Set', 'Birth certificate set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting birth certificate', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting birth certificate');
        }
    }

    public function setIDCard($file_path, Carbon $issue_date, Carbon $expiry_date, string $id_number)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->idCard()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    'id_number' => $id_number,
                ],
            );
            AppLog::info('ID Card Set', 'ID card set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting ID card', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting ID card');
        }
    }

    public function setDriverLicense($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->driverLicense()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('Driver License Set', 'Driver license set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting driver license', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting driver license');
        }
    }

    public function setEmployeeContract($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->contracts()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                    'issue_date' => $issue_date,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('Employee Contract Set', 'Employee contract set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting employee contract', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting employee contract: ' . $e->getMessage());
        }
    }

    public function setEmployeeS1Doc($file_path, Carbon $issue_date, Carbon $expiry_date, string $s1_number)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->employeeS1Doc()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    's1_number' => $s1_number,
                ],
            );
            AppLog::info('Employee S1 Doc Set', 'Employee S1 doc set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting employee S1 doc', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting employee S1 doc');
        }
    }

    public function setEmployeeS2Doc($file_path, Carbon $issue_date, ?Carbon $expiry_date = null, float $s2_amount, int $year)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            // Create a new record instead of updating existing one to support multiple S2 docs
            $this->employeeS2Doc()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                    'year' => $year,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    's2_amount' => $s2_amount,
                    'year' => $year,
                ],
            );
            AppLog::info('Employee S2 Doc Set', 'Employee S2 doc set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting employee S2 doc', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting employee S2 doc');
        }
    }

    public function setEmployeeS6Doc($file_path, Carbon $issue_date, Carbon $expiry_date, string $s6_number, string $leaving_reason)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            // Create a new record instead of updating existing one to support multiple S6 docs
            $this->employeeS6Doc()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    's6_number' => $s6_number,
                    'leaving_reason' => $leaving_reason,
                ],
            );
            AppLog::info('Employee S6 Doc Set', 'Employee S6 doc set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting employee S6 doc', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting employee S6 doc');
        }
    }

    public function setPoliceRecord($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->policeRecords()->create([
                'created_by' => $loggedInUser->id,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
            ]);
            AppLog::info('Police Record Set', 'Police record set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting police record', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting police record');
        }
    }

    public function setHrLetter($file_path, Carbon $issue_date, ?Carbon $expiry_date = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->hrLetters()->create([
                'created_by' => $loggedInUser->id,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
            ]);
            AppLog::info('HR Letter Set', 'HR letter set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting hr letter', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting hr letter');
        }
    }

    /**
     * Set medical record for the employee
     *
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon $expiry_date
     * @param string $status
     * @param string|null $insurance_number
     * @param string|null $medical_card_code
     * @param Carbon|null $medical_card_start
     * @param Carbon|null $medical_card_expiry
     * @return bool
     * @throws AppException
     */
    public function setMedicalRecord($file_path, Carbon $issue_date, Carbon $expiry_date, string $status, ?string $insurance_number = null, ?string $medical_card_code = null, ?Carbon $medical_card_start = null, ?Carbon $medical_card_expiry = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->medicalRecord()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                    'status' => $status,
                    'insurance_number' => $insurance_number,
                    'medical_card_code' => $medical_card_code,
                    'medical_card_start' => $medical_card_start,
                    'medical_card_expiry' => $medical_card_expiry,
                ],
            );
            AppLog::info('Medical Record Set', 'Medical record set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting medical record', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting medical record: ' . $e->getMessage());
        }
    }


    public function setExternalMedicalRecord($file_path, Carbon $issue_date, Carbon $expiry_date, string $id_number)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->externalMedicalRecord()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'id_number' => $id_number,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('External Medical Record Set', 'External medical record set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting external medical record', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting external medical record: ' . $e->getMessage());
        }
    }

    /**
     * Set practice card for the employee
     *
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon $expiry_date
     * @return bool
     * @throws AppException
     */
    public function setPracticeCard($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->practiceCard()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('Practice Card Set', 'Practice card set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting practice card', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting practice card: ' . $e->getMessage());
        }
    }

    /**
     * Set skills qualification for the employee
     *
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon $expiry_date
     * @return bool
     * @throws AppException
     */
    public function setSkillsQualification($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->skillsQualifications()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('Skills Qualification Set', 'Skills qualification set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting skills qualification', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting skills qualification: ' . $e->getMessage());
        }
    }

    /**
     * Set syndicate card for the employee
     *
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon $expiry_date
     * @return bool
     * @throws AppException
     */
    public function setSyndicateCard($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->syndicateCard()->updateOrCreate(
                [
                    'employee_id' => $this->id,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'issue_date' => $issue_date,
                    'expiry_date' => $expiry_date,
                ],
            );
            AppLog::info('Syndicate Card Set', 'Syndicate card set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting syndicate card', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting syndicate card: ' . $e->getMessage());
        }
    }

    /**
     * Set work declaration for the employee
     *
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon $expiry_date
     * @return bool
     * @throws AppException
     */
    public function setWorkDeclaration($file_path, Carbon $issue_date, Carbon $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->workDeclarations()->create([
                'created_by' => $loggedInUser->id,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
            ]);
            AppLog::info('Work Declaration Set', 'Work declaration set for employee: ' . $this->name, loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error setting work declaration', $e->getMessage(), loggable: $this);
            throw new AppException('Error setting work declaration: ' . $e->getMessage());
        }
    }

    /**
     * Update employee base information
     *
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $address
     * @param string $nationality
     * @param string $gender
     * @param string|Carbon $birth_date
     * @param string|Carbon $employment_date
     * @return Employee
     * @throws AppException
     */
    public function updateBaseInfo(string $name, string $name_ar, string $email, string $phone, string $address, string $nationality, string $gender, $birth_date, $employment_date, string $id_number, ?string $mother_name = null, ?Carbon $termination_date = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You do not have permission to update this employee');
        }

        try {
            $this->update([
                'name' => $name,
                'name_ar' => $name_ar,
                'email' => $email,
                'phone' => $phone,
                'id_number' => $id_number,
                'address' => $address,
                'nationality' => $nationality,
                'gender' => $gender,
                'birth_date' => $birth_date,
                'employment_date' => $employment_date,
                'mother_name' => $mother_name,
                'termination_date' => $termination_date,
            ]);
            AppLog::info('Employee Base Info Updated', 'Employee base info updated for employee: ' . $this->name, loggable: $this);
            return $this->fresh();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating employee base information', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating employee base information: ' . $e->getMessage());
        }
    }

    /**
     * Update or create employee information
     *
     * @param int $insurance_office_id
     * @param string|null $insurance_number
     * @param string|null $insurance_amount
     * @param string|null $academic_qualification
     * @param string|null $university
     * @param int|null $graduation_year
     * @param string|null $military_status
     * @param string|null $marital_status
     * @return EmployeeInfo
     * @throws AppException
     */
    public function updateEmployeeInfo(int $insurance_office_id, ?string $insurance_number = null, ?string $insurance_amount = null, ?string $academic_qualification = null, ?string $university = null, ?int $graduation_year = null, ?string $military_status = null, ?string $marital_status = null, ?int $salary_grade_id = null, ?int $vacation_package_id = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You do not have permission to update this employee information');
        }

        try {
            $employeeInfo = $this->info()->updateOrCreate(
                ['employee_id' => $this->id],
                [
                    'salary_grade_id' => $salary_grade_id,
                    'vacation_package_id' => $vacation_package_id,
                    'insurance_office_id' => $insurance_office_id,
                    'insurance_number' => $insurance_number,
                    'insurance_amount' => $insurance_amount,
                    'academic_qualification' => $academic_qualification,
                    'university' => $university,
                    'graduation_year' => $graduation_year,
                    'military_status' => $military_status,
                    'marital_status' => $marital_status,
                    'gender' => $this->gender, // Copy from employee
                ],
            );
            AppLog::info('Employee Info Updated', 'Employee info updated for employee: ' . $this->name, loggable: $this);
            return $employeeInfo;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating employee information', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating employee information: ' . $e->getMessage());
        }
    }


    //scopes
    public function scopeCurrent($query, $start_date = null)
    {
        return $query->where(function ($q) use ($start_date) {
            $q->whereNull('termination_date');
            if ($start_date)
                $q->orWhere(function ($q) use ($start_date) {
                    $q->where('termination_date', '>=', $start_date);
                });
        });
    }

    /**
     * Scope to find employees with missing documents
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMissingDocuments($query)
    {
        return $query->where(function ($query) {
            // 1. ID Card
            $query
                ->whereDoesntHave('idCard')
                // 2. Birth Certificate
                ->orWhereDoesntHave('birthCertificate')
                // 3. Employment Contract
                ->orWhereDoesntHave('contracts')
                // 4. Army Service Paper - only for males with appropriate military status
                ->orWhere(function ($query) {
                    $query
                        ->whereHas('info', function ($q) {
                            $q->where('gender', 'male')->whereNotIn('military_status', ['exempt', 'completed']);
                        })
                        ->whereDoesntHave('armyServicePaper');
                })
                // 5. Driver License - only if required
                ->orWhere(function ($query) {
                    $query->where('license_required', 1)->whereDoesntHave('driverLicense');
                })
                // 6. Police Record
                ->orWhereDoesntHave('policeRecords')
                // 7. HR Letter
                ->orWhereDoesntHave('hrLetters')
                // 8. S1 Document
                ->orWhereDoesntHave('employeeS1Doc')
                // 9. S2 Document
                ->orWhereDoesntHave('employeeS2Doc')
                // 10. S6 Document
                ->orWhereDoesntHave('employeeS6Doc')
                // 11. Medical Record
                ->orWhereDoesntHave('medicalRecord')
                // 12. External Medical Record
                ->orWhereDoesntHave('externalMedicalRecord')
                // 13. Practice Card
                ->orWhereDoesntHave('practiceCard')
                // 14. Syndicate Card
                ->orWhereDoesntHave('syndicateCard')
                // 15. Work Declaration
                ->orWhereDoesntHave('workDeclarations');
        });
    }

    /**
     * Scope to find employees with expired documents
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithExpiredDocuments($query)
    {
        $today = now()->format('Y-m-d');

        return $query->where(function ($query) use ($today) {
            // 1. ID Card expired
            $query
                ->whereHas('idCard', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 2. Birth Certificate expired
                ->orWhereHas('birthCertificate', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 3. Employment Contract expired
                ->orWhereHas('contracts', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 4. Army Service Paper expired
                ->orWhereHas('armyServicePaper', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 5. Driver License expired
                ->orWhereHas('driverLicense', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 6. Police Record expired
                ->orWhereHas('policeRecords', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 7. HR Letter expired
                ->orWhereHas('hrLetters', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 8. S1 Doc expired
                ->orWhereHas('employeeS1Doc', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 9. S2 Doc expired
                ->orWhereHas('employeeS2Doc', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 10. S6 Doc expired
                ->orWhereHas('employeeS6Doc', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 11. Medical Record expired
                ->orWhereHas('medicalRecord', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 12. External Medical Record expired
                ->orWhereHas('externalMedicalRecord', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 13. Practice Card expired
                ->orWhereHas('practiceCard', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 14. Syndicate Card expired
                ->orWhereHas('syndicateCard', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 15. Work Declaration expired
                ->orWhereHas('workDeclarations', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                });
        });
    }

    /**
     * Scope to find employees with documents near expiry
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithNearExpiryDocuments($query)
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        return $query->where(function ($query) use ($today, $nearExpiryDate) {
            // 1. ID Card near expiry
            $query
                ->whereHas('idCard', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 2. Birth Certificate near expiry
                ->orWhereHas('birthCertificate', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 3. Employment Contract near expiry
                ->orWhereHas('contracts', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 4. Army Service Paper near expiry
                ->orWhereHas('armyServicePaper', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 5. Driver License near expiry
                ->orWhereHas('driverLicense', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 6. Police Record near expiry
                ->orWhereHas('policeRecords', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 7. HR Letter near expiry
                ->orWhereHas('hrLetters', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 8. S1 Doc near expiry
                ->orWhereHas('employeeS1Doc', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 9. S2 Doc near expiry
                ->orWhereHas('employeeS2Doc', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 10. S6 Doc near expiry
                ->orWhereHas('employeeS6Doc', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 11. Medical Record near expiry
                ->orWhereHas('medicalRecord', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 12. External Medical Record near expiry
                ->orWhereHas('externalMedicalRecord', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 13. Practice Card near expiry
                ->orWhereHas('practiceCard', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 14. Syndicate Card near expiry
                ->orWhereHas('syndicateCard', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                })
                // 15. Work Declaration near expiry
                ->orWhereHas('workDeclarations', function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
                });
        });
    }

    /**
     * Scope a query to search for employees by name, email, or phone
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, ?string $search = null, ?Carbon $startDate = null, ?Carbon $endDate = null, ?string $packageId = null, ?string $departmentId = null)
    {

        $query->when($startDate, function ($query) use ($startDate) {
            $query->where('employees.created_at', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            $query->where('employees.created_at', '<=', $endDate);
        })->when($packageId, function ($query) use ($packageId) {
            $query->whereHas('benefitConfiguration', function ($q) use ($packageId) {
                $q->where('package_id', $packageId);
            });
        })->when($departmentId, function ($query) use ($departmentId) {
            $query->whereHas('position', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        })->when($search, function ($query) use ($search) {
            $splittedSearch = explode(' ', $search);
            $query->where(function ($q) use ($splittedSearch) {
                foreach ($splittedSearch as $search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                }
            });
        });

        return $query;
    }


    /**
     * Get missing documents for this employee
     *
     * @return array
     */
    public function getMissingDocuments()
    {
        $missingDocs = [];

        // 1. ID Card
        if (!$this->idCard) {
            $missingDocs[] = 'ID Card';
        }

        // 2. Birth Certificate
        if (!$this->birthCertificate) {
            $missingDocs[] = 'Birth Certificate';
        }

        // 3. Employment Contract
        if ($this->contracts->isEmpty()) {
            $missingDocs[] = 'Employment Contract';
        }

        // 4. Army Service Paper
        if ($this->gender === Applicant::GENDER_MALE && ($this->info && in_array($this->info->military_status, [Applicant::MILITARY_STATUS_EXEMPTED, Applicant::MILITARY_STATUS_COMPLETED])) && !$this->armyServicePaper) {
            $missingDocs[] = 'Army Service Paper';
        }

        // 5. Driver License
        if ($this->license_required && !$this->driverLicense) {
            $missingDocs[] = 'Driver License';
        }

        // 6. Police Record
        if ($this->policeRecords->isEmpty()) {
            $missingDocs[] = 'Police Record';
        }

        // 7. HR Letter
        if ($this->hrLetters->isEmpty()) {
            $missingDocs[] = 'HR Letter';
        }

        // 8. S1 Document
        if (!$this->employeeS1Doc) {
            $missingDocs[] = 'S1 Document';
        }

        // 9. S2 Document
        if ($this->employeeS2Doc->isEmpty()) {
            $missingDocs[] = 'S2 Document';
        }

        // 10. S6 Document
        if ($this->employeeS6Doc->isEmpty()) {
            $missingDocs[] = 'S6 Document';
        }

        // 11. Medical Record
        if (!$this->medicalRecord) {
            $missingDocs[] = 'Medical Record';
        }

        // 12. External Medical Record
        if (!$this->externalMedicalRecord) {
            $missingDocs[] = 'External Medical Record';
        }

        // 13. Practice Card
        if (!$this->practiceCard) {
            $missingDocs[] = 'Practice Card';
        }

        // 14. Syndicate Card
        if (!$this->syndicateCard) {
            $missingDocs[] = 'Syndicate Card';
        }

        // 15. Work Declaration
        if ($this->workDeclarations->isEmpty()) {
            $missingDocs[] = 'Work Declaration';
        }

        return $missingDocs;
    }

    /**
     * Get expired documents for this employee
     *
     * @return array
     */
    public function getExpiredDocuments()
    {
        $expiredDocs = [];
        $today = now();

        // 1. ID Card
        if ($this->idCard && $this->idCard->expiry_date && $today->gt($this->idCard->expiry_date)) {
            $expiredDocs[] = 'ID Card';
        }

        // 2. Birth Certificate
        if ($this->birthCertificate && $this->birthCertificate->expiry_date && $today->gt($this->birthCertificate->expiry_date)) {
            $expiredDocs[] = 'Birth Certificate';
        }

        // 3. Employment Contract
        foreach ($this->contracts as $contract) {
            if ($contract->expiry_date && $today->gt($contract->expiry_date)) {
                $expiredDocs[] = 'Employment Contract';
                break;
            }
        }

        // 4. Army Service Paper
        if ($this->armyServicePaper && $this->armyServicePaper->expiry_date && $today->gt($this->armyServicePaper->expiry_date)) {
            $expiredDocs[] = 'Army Service Paper';
        }

        // 5. Driver License
        if ($this->driverLicense && $this->driverLicense->expiry_date && $today->gt($this->driverLicense->expiry_date)) {
            $expiredDocs[] = 'Driver License';
        }

        // 6. Police Record
        foreach ($this->policeRecords as $record) {
            if ($record->expiry_date && $today->gt($record->expiry_date)) {
                $expiredDocs[] = 'Police Record';
                break;
            }
        }

        // 7. HR Letter
        foreach ($this->hrLetters as $letter) {
            if ($letter->expiry_date && $today->gt($letter->expiry_date)) {
                $expiredDocs[] = 'HR Letter';
                break;
            }
        }

        // 8. S1 Document
        if ($this->employeeS1Doc && $this->employeeS1Doc->expiry_date && $today->gt($this->employeeS1Doc->expiry_date)) {
            $expiredDocs[] = 'S1 Document';
        }

        // 9. S2 Document
        foreach ($this->employeeS2Doc as $doc) {
            if ($doc->expiry_date && $today->gt($doc->expiry_date)) {
                $expiredDocs[] = 'S2 Document';
                break;
            }
        }

        // 10. S6 Document
        foreach ($this->employeeS6Doc as $doc) {
            if ($doc->expiry_date && $today->gt($doc->expiry_date)) {
                $expiredDocs[] = 'S6 Document';
                break;
            }
        }

        // 11. Medical Record
        if ($this->medicalRecord && $this->medicalRecord->expiry_date && $today->gt($this->medicalRecord->expiry_date)) {
            $expiredDocs[] = 'Medical Record';
        }

        // 12. External Medical Record
        if ($this->externalMedicalRecord && $this->externalMedicalRecord->expiry_date && $today->gt($this->externalMedicalRecord->expiry_date)) {
            $expiredDocs[] = 'External Medical Record';
        }

        // 13. Practice Card
        if ($this->practiceCard && $this->practiceCard->expiry_date && $today->gt($this->practiceCard->expiry_date)) {
            $expiredDocs[] = 'Practice Card';
        }

        // 14. Syndicate Card
        if ($this->syndicateCard && $this->syndicateCard->expiry_date && $today->gt($this->syndicateCard->expiry_date)) {
            $expiredDocs[] = 'Syndicate Card';
        }

        // 15. Work Declaration
        foreach ($this->workDeclarations as $declaration) {
            if ($declaration->expiry_date && $today->gt($declaration->expiry_date)) {
                $expiredDocs[] = 'Work Declaration';
                break;
            }
        }

        return $expiredDocs;
    }

    /**
     * Get near expiry documents for this employee
     *
     * @return array
     */
    public function getNearExpiryDocuments()
    {
        $nearExpiryDocs = [];
        $today = now();
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS);

        // 1. ID Card
        if ($this->idCard && $this->idCard->expiry_date && $today->lt($this->idCard->expiry_date) && $nearExpiryDate->gte($this->idCard->expiry_date)) {
            $nearExpiryDocs[] = 'ID Card';
        }

        // 2. Birth Certificate
        if ($this->birthCertificate && $this->birthCertificate->expiry_date && $today->lt($this->birthCertificate->expiry_date) && $nearExpiryDate->gte($this->birthCertificate->expiry_date)) {
            $nearExpiryDocs[] = 'Birth Certificate';
        }

        // 3. Employment Contract
        foreach ($this->contracts as $contract) {
            if ($contract->expiry_date && $today->lt($contract->expiry_date) && $nearExpiryDate->gte($contract->expiry_date)) {
                $nearExpiryDocs[] = 'Employment Contract';
                break;
            }
        }

        // 4. Army Service Paper
        if ($this->armyServicePaper && $this->armyServicePaper->expiry_date && $today->lt($this->armyServicePaper->expiry_date) && $nearExpiryDate->gte($this->armyServicePaper->expiry_date)) {
            $nearExpiryDocs[] = 'Army Service Paper';
        }

        // 5. Driver License
        if ($this->driverLicense && $this->driverLicense->expiry_date && $today->lt($this->driverLicense->expiry_date) && $nearExpiryDate->gte($this->driverLicense->expiry_date)) {
            $nearExpiryDocs[] = 'Driver License';
        }

        // 6. Police Record
        foreach ($this->policeRecords as $record) {
            if ($record->expiry_date && $today->lt($record->expiry_date) && $nearExpiryDate->gte($record->expiry_date)) {
                $nearExpiryDocs[] = 'Police Record';
                break;
            }
        }

        // 7. HR Letter
        foreach ($this->hrLetters as $letter) {
            if ($letter->expiry_date && $today->lt($letter->expiry_date) && $nearExpiryDate->gte($letter->expiry_date)) {
                $nearExpiryDocs[] = 'HR Letter';
                break;
            }
        }

        // 8. S1 Document
        if ($this->employeeS1Doc && $this->employeeS1Doc->expiry_date && $today->lt($this->employeeS1Doc->expiry_date) && $nearExpiryDate->gte($this->employeeS1Doc->expiry_date)) {
            $nearExpiryDocs[] = 'S1 Document';
        }

        // 9. S2 Document
        foreach ($this->employeeS2Doc as $doc) {
            if ($doc->expiry_date && $today->lt($doc->expiry_date) && $nearExpiryDate->gte($doc->expiry_date)) {
                $nearExpiryDocs[] = 'S2 Document';
                break;
            }
        }

        // 10. S6 Document
        foreach ($this->employeeS6Doc as $doc) {
            if ($doc->expiry_date && $today->lt($doc->expiry_date) && $nearExpiryDate->gte($doc->expiry_date)) {
                $nearExpiryDocs[] = 'S6 Document';
                break;
            }
        }

        // 11. Medical Record
        if ($this->medicalRecord && $this->medicalRecord->expiry_date && $today->lt($this->medicalRecord->expiry_date) && $nearExpiryDate->gte($this->medicalRecord->expiry_date)) {
            $nearExpiryDocs[] = 'Medical Record';
        }

        // 12. External Medical Record
        if ($this->externalMedicalRecord && $this->externalMedicalRecord->expiry_date && $today->lt($this->externalMedicalRecord->expiry_date) && $nearExpiryDate->gte($this->externalMedicalRecord->expiry_date)) {
            $nearExpiryDocs[] = 'External Medical Record';
        }

        // 13. Practice Card
        if ($this->practiceCard && $this->practiceCard->expiry_date && $today->lt($this->practiceCard->expiry_date) && $nearExpiryDate->gte($this->practiceCard->expiry_date)) {
            $nearExpiryDocs[] = 'Practice Card';
        }

        // 14. Syndicate Card
        if ($this->syndicateCard && $this->syndicateCard->expiry_date && $today->lt($this->syndicateCard->expiry_date) && $nearExpiryDate->gte($this->syndicateCard->expiry_date)) {
            $nearExpiryDocs[] = 'Syndicate Card';
        }

        // 15. Work Declaration
        foreach ($this->workDeclarations as $declaration) {
            if ($declaration->expiry_date && $today->lt($declaration->expiry_date) && $nearExpiryDate->gte($declaration->expiry_date)) {
                $nearExpiryDocs[] = 'Work Declaration';
                break;
            }
        }

        return $nearExpiryDocs;
    }

    //// Dashboard Statistics ////
    /**
     * Get ID card statistics for dashboard
     *
     * @return array
     */
    public static function getIdCardStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing ID cards
        $missing = self::whereDoesntHave('idCard')->count();

        // Get employees with expired ID cards
        $expired = self::whereHas('idCard', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with ID cards near expiry
        $nearExpiry = self::whereHas('idCard', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid ID cards
        $valid = self::whereHas('idCard', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get birth certificate statistics for dashboard
     *
     * @return array
     */
    public static function getBirthCertificateStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing birth certificates
        $missing = self::whereDoesntHave('birthCertificate')->count();

        // Get employees with expired birth certificates
        $expired = self::whereHas('birthCertificate', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with birth certificates near expiry
        $nearExpiry = self::whereHas('birthCertificate', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid birth certificates
        $valid = self::whereHas('birthCertificate', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        // Get counts by type
        $original = self::whereHas('birthCertificate', function ($q) {
            $q->where('type', 'Original');
        })->count();

        $verifiedCopy = self::whereHas('birthCertificate', function ($q) {
            $q->where('type', 'Verified Copy');
        })->count();

        $copy = self::whereHas('birthCertificate', function ($q) {
            $q->where('type', 'Copy');
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
            'by_type' => [
                'original' => $original,
                'verified_copy' => $verifiedCopy,
                'copy' => $copy,
            ],
        ];
    }

    /**
     * Get army service paper statistics for dashboard
     *
     * @return array
     */
    public static function getArmyServicePaperStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get female employees (not required to have army service paper)
        $females = self::where('gender', 'female')->count();

        // Get male employees (required to have army service paper)
        $males = self::where('gender', 'male')->count();

        // Get male employees with missing army service papers
        $missing = self::where('gender', 'male')
            ->whereDoesntHave('armyServicePaper')
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        // Get male employees with expired army service papers
        $expired = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) use ($today) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        // Get male employees with army service papers near expiry
        $nearExpiry = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) use ($today, $nearExpiryDate) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        // Get male employees with valid army service papers
        $valid = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) use ($today, $nearExpiryDate) {
                $q->where(function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
                });
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        // Get counts by type
        $original = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) {
                $q->where('type', 'Original');
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        $verifiedCopy = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) {
                $q->where('type', 'Verified Copy');
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        $copy = self::where('gender', 'male')
            ->whereHas('armyServicePaper', function ($q) {
                $q->where('type', 'Copy');
            })
            ->whereHas('info', function ($q) {
                $q->whereIn('military_status', ['exempt', 'completed']);
            })
            ->count();

        return [
            'total' => $total,
            'females' => $females,
            'males' => $males,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
            'by_type' => [
                'original' => $original,
                'verified_copy' => $verifiedCopy,
                'copy' => $copy,
            ],
        ];
    }

    /**
     * Get employment contract statistics for dashboard
     *
     * @return array
     */
    public static function getEmploymentContractStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing contracts
        $missing = self::whereDoesntHave('contracts')->count();

        // Get employees with expired contracts
        $expired = self::whereHas('contracts', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with contracts near expiry
        $nearExpiry = self::whereHas('contracts', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid contracts
        $valid = self::whereHas('contracts', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get driver license statistics for dashboard
     *
     * @return array
     */
    public static function getDriverLicenseStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees who require a driver license
        $required = self::where('license_required', true)->count();

        // Get employees who don't require a driver license
        $notRequired = self::where('license_required', false)->count();

        // Get employees with missing driver licenses (only for those who require it)
        $missing = self::where('license_required', true)->whereDoesntHave('driverLicense')->count();

        // Get employees with expired driver licenses (only for those who require it)
        $expired = self::where('license_required', true)
            ->whereHas('driverLicense', function ($q) use ($today) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
            })
            ->count();

        // Get employees with driver licenses near expiry (only for those who require it)
        $nearExpiry = self::where('license_required', true)
            ->whereHas('driverLicense', function ($q) use ($today, $nearExpiryDate) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
            })
            ->count();

        // Get employees with valid driver licenses (only for those who require it)
        $valid = self::where('license_required', true)
            ->whereHas('driverLicense', function ($q) use ($today, $nearExpiryDate) {
                $q->where(function ($q) use ($today, $nearExpiryDate) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
                });
            })
            ->count();

        return [
            'total' => $total,
            'required' => $required,
            'not_required' => $notRequired,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get police record statistics for dashboard
     *
     * @return array
     */
    public static function getPoliceRecordStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing police records
        $missing = self::whereDoesntHave('policeRecords')->count();

        // Get employees with expired police records
        $expired = self::whereHas('policeRecords', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with police records near expiry
        $nearExpiry = self::whereHas('policeRecords', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid police records
        $valid = self::whereHas('policeRecords', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get HR letter statistics for dashboard
     *
     * @return array
     */
    public static function getHrLetterStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing HR letters
        $missing = self::whereDoesntHave('hrLetters')->count();

        // Get employees with expired HR letters
        $expired = self::whereHas('hrLetters', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with HR letters near expiry
        $nearExpiry = self::whereHas('hrLetters', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid HR letters
        $valid = self::whereHas('hrLetters', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get S1 document statistics for dashboard
     *
     * @return array
     */
    public static function getS1DocStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing S1 documents
        $missing = self::whereDoesntHave('employeeS1Doc')->count();

        // Get employees with expired S1 documents
        $expired = self::whereHas('employeeS1Doc', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with S1 documents near expiry
        $nearExpiry = self::whereHas('employeeS1Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid S1 documents
        $valid = self::whereHas('employeeS1Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get S2 document statistics for dashboard
     *
     * @return array
     */
    public static function getS2DocStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing S2 documents
        $missing = self::whereDoesntHave('employeeS2Doc')->count();

        // Get employees with expired S2 documents
        $expired = self::whereHas('employeeS2Doc', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with S2 documents near expiry
        $nearExpiry = self::whereHas('employeeS2Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid S2 documents
        $valid = self::whereHas('employeeS2Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get S6 document statistics for dashboard
     *
     * @return array
     */
    public static function getS6DocStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing S6 documents
        $missing = self::whereDoesntHave('employeeS6Doc')->count();

        // Get employees with expired S6 documents
        $expired = self::whereHas('employeeS6Doc', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with S6 documents near expiry
        $nearExpiry = self::whereHas('employeeS6Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid S6 documents
        $valid = self::whereHas('employeeS6Doc', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get medical record statistics
     *
     * @return array
     */
    public static function getMedicalRecordStatistics()
    {
        $total = self::count();
        $valid = self::whereHas('medicalRecord', function ($q) {
            $q->where('expiry_date', '>', now());
        })->count();
        $expired = self::whereHas('medicalRecord', function ($q) {
            $q->where('expiry_date', '<', now());
        })->count();
        $missing = self::whereDoesntHave('medicalRecord')->count();
        $nearExpiry = self::whereHas('medicalRecord', function ($q) {
            $q->where('expiry_date', '>', now())->where('expiry_date', '<', now()->addDays(self::NEAR_EXPIRY_DAYS));
        })->count();

        // Count by status
        $byStatus = [
            'Not Covered' => self::whereHas('medicalRecord', function ($q) {
                $q->where('status', 'Not Covered');
            })->count(),
            'Examination' => self::whereHas('medicalRecord', function ($q) {
                $q->where('status', 'Examination');
            })->count(),
            'Issuing' => self::whereHas('medicalRecord', function ($q) {
                $q->where('status', 'Issuing');
            })->count(),
            'Covered' => self::whereHas('medicalRecord', function ($q) {
                $q->where('status', 'Covered');
            })->count(),
            'External Cover' => self::whereHas('medicalRecord', function ($q) {
                $q->where('status', 'External Cover');
            })->count(),
        ];

        return [
            'total' => $total,
            'valid' => $valid,
            'expired' => $expired,
            'missing' => $missing,
            'near_expiry' => $nearExpiry,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Get external medical record statistics
     *
     * @return array
     */
    public static function getExternalMedicalRecordStatistics()
    {
        $total = self::count();
        $valid = self::whereHas('externalMedicalRecord', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            });
        })->count();
        $expired = self::whereHas('externalMedicalRecord', function ($q) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<', now());
        })->count();
        $missing = self::whereDoesntHave('externalMedicalRecord')->count();
        $nearExpiry = self::whereHas('externalMedicalRecord', function ($q) {
            $q->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(self::NEAR_EXPIRY_DAYS));
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get practice card statistics
     *
     * @return array
     */
    public static function getPracticeCardStatistics()
    {
        $total = self::count();
        $valid = self::whereHas('practiceCard', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            });
        })->count();
        $expired = self::whereHas('practiceCard', function ($q) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<', now());
        })->count();
        $missing = self::whereDoesntHave('practiceCard')->count();
        $nearExpiry = self::whereHas('practiceCard', function ($q) {
            $q->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(self::NEAR_EXPIRY_DAYS));
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get skills qualification statistics
     *
     * @return array
     */
    public static function getSkillsQualificationStatistics()
    {
        $total = self::count();
        $valid = self::whereHas('skillsQualifications', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            });
        })->count();
        $expired = self::whereHas('skillsQualifications', function ($q) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<', now());
        })->count();
        $missing = self::whereDoesntHave('skillsQualifications')->count();
        $nearExpiry = self::whereHas('skillsQualifications', function ($q) {
            $q->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(self::NEAR_EXPIRY_DAYS));
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get syndicate card statistics
     *
     * @return array
     */
    public static function getSyndicateCardStatistics()
    {
        $total = self::count();
        $valid = self::whereHas('syndicateCard', function ($q) {
            $q->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            });
        })->count();
        $expired = self::whereHas('syndicateCard', function ($q) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<', now());
        })->count();
        $missing = self::whereDoesntHave('syndicateCard')->count();
        $nearExpiry = self::whereHas('syndicateCard', function ($q) {
            $q->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<', now()->addDays(self::NEAR_EXPIRY_DAYS));
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /**
     * Get work declaration statistics for dashboard
     *
     * @return array
     */
    public static function getWorkDeclarationStatistics()
    {
        $today = now()->format('Y-m-d');
        $nearExpiryDate = now()->addDays(self::NEAR_EXPIRY_DAYS)->format('Y-m-d');

        // Get total employees
        $total = self::count();

        // Get employees with missing work declarations
        $missing = self::whereDoesntHave('workDeclarations')->count();

        // Get employees with expired work declarations
        $expired = self::whereHas('workDeclarations', function ($q) use ($today) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '<=', $today);
        })->count();

        // Get employees with work declarations near expiry
        $nearExpiry = self::whereHas('workDeclarations', function ($q) use ($today, $nearExpiryDate) {
            $q->whereNotNull('expiry_date')->where('expiry_date', '>', $today)->where('expiry_date', '<=', $nearExpiryDate);
        })->count();

        // Get employees with valid work declarations
        $valid = self::whereHas('workDeclarations', function ($q) use ($today, $nearExpiryDate) {
            $q->where(function ($q) use ($today, $nearExpiryDate) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $nearExpiryDate);
            });
        })->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'near_expiry' => $nearExpiry,
            'expired' => $expired,
            'missing' => $missing,
        ];
    }

    /// attribute
    public function getIsManagerAttribute()
    {
        $position = $this->position()->first();
        return $position ? $position->children()->count() > 0 : false;
    }

    public function getManagerIdAttribute()
    {
        return $this->benefitConfiguration?->manager_id;
    }

    public function getInsuranceAmountAttribute()
    {
        return $this->benefitConfiguration?->insurance_amount;
    }

    public function getGrossSalaryAttribute()
    {
        return $this->benefitConfiguration?->gross_salary;
    }

    public function getBasicSalaryAttribute()
    {
        return $this->baseBenefits?->where('name', 'basic')->first()?->amount;
    }

    //// relations ////
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function position()
    {
        return $this->hasOne(Position::class);
    }

    public function info()
    {
        return $this->hasOne(EmployeeInfo::class);
    }

    public function benefitConfiguration()
    {
        return $this->hasOne(BenefitConfiguration::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function birthCertificate()
    {
        return $this->hasOne(BirthCertificate::class);
    }

    public function armyServicePaper()
    {
        return $this->hasOne(ArmyServicePaper::class);
    }

    public function workDeclarations()
    {
        return $this->hasMany(WorkDeclaration::class);
    }

    public function policeRecords()
    {
        return $this->hasMany(PoliceRecord::class);
    }

    public function hrLetters()
    {
        return $this->hasMany(HrLetter::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function externalMedicalRecord()
    {
        return $this->hasOne(ExternalMedicalRecord::class);
    }

    public function idCard()
    {
        return $this->hasOne(IDCard::class);
    }

    public function driverLicense()
    {
        return $this->hasOne(DriverLicense::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function syndicateCard()
    {
        return $this->hasOne(SyndicateCard::class);
    }

    public function skillsQualifications()
    {
        return $this->hasOne(SkillsQualification::class);
    }

    public function practiceCard()
    {
        return $this->hasOne(PracticeCard::class);
    }

    public function employeeS1Doc()
    {
        return $this->hasOne(EmployeeS1Doc::class);
    }

    public function employeeS2Doc()
    {
        return $this->hasMany(EmployeeS2Doc::class);
    }

    public function employeeS6Doc()
    {
        return $this->hasMany(EmployeeS6Doc::class);
    }

    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }

    public function referredBy()
    {
        return $this->hasOneThrough(Employee::class, Application::class, 'referred_by_id');
    }

    public function applications()
    {
        return $this->hasOne(Application::class);
    }

    public function previousEmployee()
    {
        return $this->hasOneThrough(Employee::class, EmployeeInfo::class, 'previous_employee_id');
    }

    public function insuranceOffice()
    {
        return $this->hasOneThrough(InsuranceOffice::class, EmployeeInfo::class, 'insurance_office_id');
    }

    public function birthPlace()
    {
        return $this->belongsTo(City::class, 'birth_place_id');
    }

    //// benefit relations ////
    public function vacationBenefits()
    {
        return $this->hasMany(VacationBenefit::class);
    }

    public function appliedVacations()
    {
        return $this->hasMany(AppliedVacation::class);
    }

    public function gainedVacations()
    {
        return $this->hasMany(GainedVacation::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function baseBenefits()
    {
        return $this->hasMany(BaseBenefit::class);
    }

    public function workingDays()
    {
        return $this->hasMany(WorkingDay::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }


    //// document status check ////
    /**
     * Check document validity status
     *
     * @param mixed $document The document to check
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status (valid, near_expiry, expired, missing)
     */
    protected function checkDocumentStatus($document, $nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        if (!$document) {
            return self::DOC_STATUS_MISSING;
        }

        // If document has no expiry date, it's considered valid
        if (!isset($document->expiry_date) || $document->expiry_date === null) {
            return self::DOC_STATUS_VALID;
        }

        $now = Carbon::now();
        $expiryDate = Carbon::parse($document->expiry_date);

        if ($expiryDate->isPast()) {
            return self::DOC_STATUS_EXPIRED;
        }

        // Calculate days until expiry
        $daysUntilExpiry = $now->diffInDays($expiryDate, false);

        // Check if document is near expiry (only if it's within the threshold and in the future)
        if ($daysUntilExpiry >= 0 && $daysUntilExpiry <= $nearExpiryDays) {
            return self::DOC_STATUS_NEAR_EXPIRY;
        }

        return self::DOC_STATUS_VALID;
    }

    /**
     * Check identity card status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkIDCardStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->idCard, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'ID Card expired on ' . ($this->idCard ? $this->idCard->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'ID Card will expire on ' . ($this->idCard ? $this->idCard->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No ID Card found' : 'ID Card is valid')),
        ];
    }

    /**
     * Check birth certificate status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkBirthCertificateStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->birthCertificate, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Birth certificate expired on ' . ($this->birthCertificate ? $this->birthCertificate->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Birth certificate will expire on ' . ($this->birthCertificate ? $this->birthCertificate->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No birth certificate found' : 'Birth certificate is valid')),
        ];
    }

    /**
     * Check army service paper status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkArmyServicePaperStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->armyServicePaper, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Army service paper expired on ' . ($this->armyServicePaper ? $this->armyServicePaper->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Army service paper will expire on ' . ($this->armyServicePaper ? $this->armyServicePaper->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No army service paper found' : 'Army service paper is valid')),
        ];
    }

    /**
     * Check driver license status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkDriverLicenseStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->driverLicense, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Driver license expired on ' . ($this->driverLicense ? $this->driverLicense->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Driver license will expire on ' . ($this->driverLicense ? $this->driverLicense->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No driver license found' : 'Driver license is valid')),
        ];
    }

    /**
     * Check employee contract status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkContractStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        // Check the most recent contract
        $latestContract = $this->contracts()->latest('issue_date')->first();
        $status = $this->checkDocumentStatus($latestContract, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Contract expired on ' . ($latestContract ? $latestContract->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Contract will expire on ' . ($latestContract ? $latestContract->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No contract found' : 'Contract is valid')),
        ];
    }

    /**
     * Check employee S1 doc status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkS1DocStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->employeeS1Doc, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'S1 document expired on ' . ($this->employeeS1Doc ? $this->employeeS1Doc->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'S1 document will expire on ' . ($this->employeeS1Doc ? $this->employeeS1Doc->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No S1 document found' : 'S1 document is valid')),
        ];
    }

    /**
     * Check employee S2 docs status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkS2DocsStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $s2Docs = $this->employeeS2Doc;

        if ($s2Docs->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No S2 documents found',
            ];
        }

        $validDocs = 0;
        $expiredDocs = 0;
        $nearExpiryDocs = 0;
        $details = [];

        foreach ($s2Docs as $doc) {
            $status = $this->checkDocumentStatus($doc, $nearExpiryDays);

            switch ($status) {
                case self::DOC_STATUS_VALID:
                    $validDocs++;
                    break;
                case self::DOC_STATUS_NEAR_EXPIRY:
                    $nearExpiryDocs++;
                    $details[] = "S2 document for year {$doc->year} will expire on " . $doc->expiry_date;
                    break;
                case self::DOC_STATUS_EXPIRED:
                    $expiredDocs++;
                    $details[] = "S2 document for year {$doc->year} expired on " . $doc->expiry_date;
                    break;
            }
        }

        if ($validDocs > 0) {
            $status = self::DOC_STATUS_VALID;
            if ($nearExpiryDocs > 0) {
                $status = self::DOC_STATUS_NEAR_EXPIRY;
            }
        } elseif ($nearExpiryDocs > 0) {
            $status = self::DOC_STATUS_NEAR_EXPIRY;
        } else {
            $status = self::DOC_STATUS_EXPIRED;
        }

        return [
            'status' => $status,
            'valid_count' => $validDocs,
            'near_expiry_count' => $nearExpiryDocs,
            'expired_count' => $expiredDocs,
            'details' => $details,
        ];
    }

    /**
     * Check employee S6 docs status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkS6DocsStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $s6Docs = $this->employeeS6Doc;

        if ($s6Docs->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No S6 documents found',
            ];
        }

        $latestS6Doc = $s6Docs->sortByDesc('issue_date')->first();
        $status = $this->checkDocumentStatus($latestS6Doc, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'S6 document expired on ' . $latestS6Doc->expiry_date : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'S6 document will expire on ' . $latestS6Doc->expiry_date : 'Latest S6 document is valid'),
        ];
    }

    /**
     * Check police record status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkPoliceRecordStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $records = $this->policeRecords;

        if ($records->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No police records found',
            ];
        }

        $latestRecord = $records->sortByDesc('issue_date')->first();
        $status = $this->checkDocumentStatus($latestRecord, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Police record expired on ' . $latestRecord->expiry_date : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Police record will expire on ' . $latestRecord->expiry_date : 'Latest police record is valid'),
        ];
    }

    /**
     * Check medical record status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkMedicalRecordStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->medicalRecord, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Medical record expired on ' . ($this->medicalRecord ? $this->medicalRecord->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Medical record will expire on ' . ($this->medicalRecord ? $this->medicalRecord->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No medical record found' : 'Medical record is valid')),
        ];
    }



    /**
     * Check external medical record status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkExternalMedicalRecordStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $status = $this->checkDocumentStatus($this->externalMedicalRecord, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'External medical record expired on ' . ($this->externalMedicalRecord ? $this->externalMedicalRecord->expiry_date : 'N/A') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'External medical record will expire on ' . ($this->externalMedicalRecord ? $this->externalMedicalRecord->expiry_date : 'N/A') : ($status == self::DOC_STATUS_MISSING ? 'No external medical record found' : 'External medical record is valid')),
        ];
    }

    /**
     * Check syndicate card status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkSyndicateCardStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        return $this->checkDocumentStatus($this->syndicateCard, $nearExpiryDays);
    }

    /**
     * Check skills qualification status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkSkillsQualificationStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        return $this->checkDocumentStatus($this->skillsQualifications, $nearExpiryDays);
    }

    /**
     * Check practice card status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkPracticeCardStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        return $this->checkDocumentStatus($this->practiceCard, $nearExpiryDays);
    }

    /**
     * Check HR letters status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkHrLettersStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $letters = $this->hrLetters;

        if ($letters->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No HR letters found',
            ];
        }

        $latestLetter = $letters->sortByDesc('issue_date')->first();
        $status = $this->checkDocumentStatus($latestLetter, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'HR letter expired on ' . $latestLetter->expiry_date : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'HR letter will expire on ' . $latestLetter->expiry_date : 'Latest HR letter is valid'),
        ];
    }

    /**
     * Check bank accounts status
     *
     * @return array Document status and details
     */
    public function checkBankAccountsStatus()
    {
        $accounts = $this->bankAccounts;

        if ($accounts->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No bank accounts found',
            ];
        }

        return [
            'status' => self::DOC_STATUS_VALID,
            'count' => $accounts->count(),
            'details' => 'Employee has ' . $accounts->count() . ' bank account(s)',
        ];
    }

    /**
     * Check work declarations status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document status and details
     */
    public function checkWorkDeclarationstatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $declarations = $this->workDeclarations;

        if ($declarations->isEmpty()) {
            return [
                'status' => self::DOC_STATUS_MISSING,
                'details' => 'No work declarations found',
            ];
        }

        $latestDeclaration = $declarations->sortByDesc('issue_date')->first();
        $status = $this->checkDocumentStatus($latestDeclaration, $nearExpiryDays);

        return [
            'status' => $status,
            'details' => $status == self::DOC_STATUS_EXPIRED ? 'Work declaration expired on ' . $latestDeclaration->expiry_date->format('Y-m-d') : ($status == self::DOC_STATUS_NEAR_EXPIRY ? 'Work declaration will expire on ' . $latestDeclaration->expiry_date->format('Y-m-d') : 'Latest work declaration is valid'),
        ];
    }

    /**
     * Check all documents status and return a summary
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return array Document statuses summary
     */
    public function checkAllDocumentsStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        $summary = [
            'id_card' => $this->checkIDCardStatus($nearExpiryDays),
            'birth_certificate' => $this->checkBirthCertificateStatus($nearExpiryDays),
            'army_service_paper' => $this->checkArmyServicePaperStatus($nearExpiryDays),
            'driver_license' => $this->checkDriverLicenseStatus($nearExpiryDays),
            'contract' => $this->checkContractStatus($nearExpiryDays),
            's1_doc' => $this->checkS1DocStatus($nearExpiryDays),
            's2_docs' => $this->checkS2DocsStatus($nearExpiryDays),
            's6_docs' => $this->checkS6DocsStatus($nearExpiryDays),
            'police_record' => $this->checkPoliceRecordStatus($nearExpiryDays),
            'medical_record' => $this->checkMedicalRecordStatus($nearExpiryDays),
            'external_medical_record' => $this->checkExternalMedicalRecordStatus($nearExpiryDays),
            'syndicate_card' => $this->checkSyndicateCardStatus($nearExpiryDays),
            'skills_qualification' => $this->checkSkillsQualificationStatus($nearExpiryDays),
            'practice_card' => $this->checkPracticeCardStatus($nearExpiryDays),
            'hr_letters' => $this->checkHrLettersStatus($nearExpiryDays),
            'bank_accounts' => $this->checkBankAccountsStatus(),
            'work_declarations' => $this->checkWorkDeclarationsStatus($nearExpiryDays),
        ];

        // Compute overall status
        $hasMissing = false;
        $hasExpired = false;
        $hasNearExpiry = false;

        foreach ($summary as $docType => $status) {
            $statusValue = is_array($status) ? $status['status'] : $status;

            if ($statusValue === self::DOC_STATUS_MISSING) {
                $hasMissing = true;
            } elseif ($statusValue === self::DOC_STATUS_EXPIRED) {
                $hasExpired = true;
            } elseif ($statusValue === self::DOC_STATUS_NEAR_EXPIRY) {
                $hasNearExpiry = true;
            }
        }

        if ($hasExpired) {
            $overallStatus = self::DOC_STATUS_EXPIRED;
        } elseif ($hasNearExpiry) {
            $overallStatus = self::DOC_STATUS_NEAR_EXPIRY;
        } elseif ($hasMissing) {
            $overallStatus = self::DOC_STATUS_MISSING;
        } else {
            $overallStatus = self::DOC_STATUS_VALID;
        }

        $summary['overall_status'] = $overallStatus;

        return $summary;
    }

    public function importEmployeesFromCSV($file_path) {}

    /**
     * Create a new employee with basic information and optional employee info
     *
     * @param int $user_id
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $address
     * @param string $nationality
     * @param string $gender
     * @param string|Carbon $birth_date
     * @param string|Carbon $employment_date
     * @return Employee
     * @throws AppException
     */
    public static function createEmployee(
        int $user_id,
        string $name,
        string $name_ar,
        string $email,
        string $phone,
        string $address,
        string $nationality,
        string $gender,
        $birth_date,
        string $id_number,
        bool $license_required,
        $employment_date,
        int $birth_place_id,
        array $employeeInfoData = [],
        ?int $applicant_id = null,
        ?string $id_card_file_path = null,
        ?string $id_issue_date = null,
        ?string $id_expiry_date = null,
        ?string $mother_name = null,
        string $status = self::STATUS_ACTIVE
    ) {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', Employee::class)) {
            throw new AppException('You do not have permission to create employees');
        }

        try {
            $employee = self::create([
                'user_id' => $user_id,
                'created_by' => $loggedInUser->id,
                'name' => $name,
                'name_ar' => $name_ar,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'nationality' => $nationality,
                'gender' => $gender,
                'birth_date' => $birth_date,
                'birth_place_id' => $birth_place_id,
                'license_required' => $license_required,
                'employment_date' => $employment_date,
                'applicant_id' => $applicant_id,
                'mother_name' => $mother_name,
                'id_number' => $id_number,
                'status' => $status,
            ]);

            if ($id_card_file_path) {
                $employee->setIDCard(
                    $id_card_file_path,
                    Carbon::parse($id_issue_date),
                    Carbon::parse($id_expiry_date),
                    $id_number
                );
            }

            // Create employee info if data is provided
            if (!empty($employeeInfoData) && isset($employeeInfoData['insurance_office_id'])) {
                $employee->info()->create(array_merge(
                    $employeeInfoData,
                    ['gender' => $gender] // Copy gender from employee
                ));
            }

            AppLog::info('Employee Created', 'Employee ' . $name . ' created successfully', loggable: $employee);
            return $employee;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating employee', $e->getMessage());
            throw new AppException('Error creating employee: ' . $e->getMessage());
            return false;
        }
    }

    public function hrLetterRequests()
    {
        return $this->hasMany(EmployeeHrLetterRequest::class);
    }

    public function setStatus(string $status)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You do not have permission to update employee status');
        }
        try {
            $this->status = $status;
            $this->save();
            AppLog::info('Employee Status Updated', 'Employee status updated for employee: ' . $this->name . ' to ' . ucfirst(str_replace('_', ' ', $status)), loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating employee status', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating employee status: ' . $e->getMessage());
        }
    }


    public function getEmployeeBaseBenefitsCalculation(Carbon $startDate, Carbon $endDate)
    {
        $activeBenefits = $this->baseBenefits()
            ->where('receiver', PackageDetail::RECEIVER_EMPLOYEE)
            ->current($startDate)
            ->get();

        $amount = 0;
        $noOfDays = $startDate->diffInDays($endDate, true);
        foreach ($activeBenefits as $benefit) {
            $amountRatio = 100;
            if ($benefit->end_date) {
                $amountRatio = $benefit->end_date->diffInDays($startDate, true) / $noOfDays;
            } else if ($benefit->start_date->isAfter($startDate)) {
                $amountRatio = $benefit->start_date->diffInDays($startDate, true) / $noOfDays;
            }
            $amount += ($benefit->amount * ($amountRatio / 100)) * days_coefficient($noOfDays, $benefit->type);
        }
        return $amount;
    }

    public function getOtherBaseBenefitsCalculation($startDate, $endDate)
    {
        $activeBenefits = $this->baseBenefits()
            ->where('receiver', PackageDetail::RECEIVER_OTHER)
            ->current($startDate)
            ->get();

        $amount = 0;
        $noOfDays = $startDate->diffInDays($endDate, true);
        foreach ($activeBenefits as $benefit) {
            $amountRatio = 100;
            if ($benefit->end_date) {
                $amountRatio = $benefit->end_date->diffInDays($startDate, true) / $noOfDays;
            } else if ($benefit->start_date->isAfter($startDate)) {
                $amountRatio = $benefit->start_date->diffInDays($startDate, true) / $noOfDays;
            }
            $amount += $benefit->amount * ($amountRatio / 100) * days_coefficient($noOfDays, $benefit->type);
        }
        return $amount;
    }


    public function activeMedicalBenefits($startDate)
    {
        return $this->baseBenefits()
            ->current($startDate)
            ->where('receiver', PackageDetail::RECEIVER_MEDICAL);
    }

    /**
     * Calculate total worked hours for an employee in a specified date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param bool $includeExtraHours Whether to include approved extra hours in the calculation
     * @return float Total worked hours
     */
    public function getWorkedHours($startDate, $endDate, $includeExtraHours = true)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $attendanceQuery = $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true);

        $totalHours = $attendanceQuery->sum('hours');

        if ($includeExtraHours) {
            $extraHours = $attendanceQuery
                ->where('is_extra_hours_approved', true)
                ->sum('extra_hours');

            $totalHours += $extraHours;
        }

        return $totalHours;
    }

    /**
     * Get attendance records that fell short of daily required hours
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return \Illuminate\Support\Collection Collection of attendance records with shortfall data
     */
    public function getShortfallHours($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $dailyRequired = $this->benefitConfiguration->daily_working_hours ?? 0;

        // Get public holidays in the date range
        $publicHolidays = PublicHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $attendances = $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->get();

        return $attendances->map(function ($attendance) use ($dailyRequired) {
            $shortfall = max(0, $dailyRequired - $attendance->hours);
            return [
                'attendance' => $attendance,
                'date' => $attendance->date,
                'actual_hours' => $attendance->hours,
                'required_hours' => $dailyRequired,
                'shortfall' => $shortfall
            ];
        })->filter(function ($item) {
            return $item['shortfall'] > 0;
        });
    }

    /**
     * Calculate total shortfall hours for an employee in a specified date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total hours short of requirement
     */
    public function getTotalShortfallHours($startDate, $endDate)
    {
        return $this->getShortfallHours($startDate, $endDate)
            ->sum('shortfall');
    }

    /**
     * Calculate deduction amount based on shortfall hours
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param float $hourlyRate The hourly rate for deduction calculation
     * @return float Deduction amount
     */
    public function calculateHourlyDeduction($startDate, $endDate, $hourlyRate = null)
    {
        if ($hourlyRate === null) {
            // Calculate hourly rate based on gross salary
            $grossSalary = $this->benefitConfiguration->gross_salary ?? 0;
            $workingDaysPerMonth = $this->workingDays->count() * 4; // Approximate
            $dailyHours = $this->benefitConfiguration->daily_working_hours ?? 8;

            $hourlyRate = $grossSalary / ($workingDaysPerMonth * $dailyHours);
        }

        $shortfallHours = $this->getTotalShortfallHours($startDate, $endDate);
        return $shortfallHours * $hourlyRate;
    }

    /**
     * Get missed working days in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return \Illuminate\Support\Collection Collection of dates that were working days but had no attendance
     */
    public function getMissedWorkingDays($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        // Get the working days of the employee
        $workingDays = $this->workingDays->pluck('type')->toArray();

        // Get public holidays in the date range
        $publicHolidays = PublicHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $period = [];
        $currentDate = $startDate->copy();

        // Generate all dates in the range
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->format('Y-m-d');

            // Check if the day of the week is a working day AND it's not a public holiday
            if (
                in_array(strtolower($currentDate->format('l')), array_map('strtolower', $workingDays))
                && !in_array($dateString, $publicHolidays)
            ) {
                $period[] = $dateString;
            }
            $currentDate->addDay();
        }

        // Get all dates with approved attendance
        $attendedDates = $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->pluck('date')
            ->toArray();

        // Return the difference - days that should have been worked but weren't
        return collect(array_diff($period, $attendedDates));
    }

    /**
     * Get missed working hours in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total hours that should have been worked but weren't
     */
    public function getMissedWorkingHours($startDate, $endDate)
    {
        $missedDays = $this->getMissedWorkingDays($startDate, $endDate);
        $dailyHours = $this->benefitConfiguration?->daily_working_hours ?? 8;

        return $missedDays->count() * $dailyHours;
    }

    /**
     * Calculate deduction amount for missed days
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param float $hourlyRate The hourly rate for deduction calculation
     * @return float Deduction amount
     */
    public function calculateMissedHoursDeduction($startDate, $endDate, $hourlyRate = null)
    {
        if ($hourlyRate === null) {
            // Calculate hourly rate based on gross salary
            $grossSalary = $this->benefitConfiguration->gross_salary ?? 0;
            $workingDaysPerMonth = $this->workingDays->count() * 4; // Approximate
            $dailyHours = $this->benefitConfiguration->daily_working_hours ?? 8;

            $hourlyRate = $grossSalary / ($workingDaysPerMonth * $dailyHours);
        }

        $missedHours = $this->getMissedWorkingHours($startDate, $endDate);
        return $missedHours * $hourlyRate;
    }

    /**
     * Get late minutes for a specific date
     *
     * @param Carbon|string $date The date to check
     * @return int|null Minutes late or null if no attendance or no configuration or public holiday
     */
    public function getLateMinutesOnDate($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Check if this date is a public holiday - no penalty on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return null; // No penalty on public holidays
        }

        $attendance = $this->attendances()
            ->where('date', $date->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->first();

        if (!$attendance) {
            return null; // No attendance record
        }

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig || !$benefitConfig->working_day_start_max) {
            return null; // No benefit configuration or start time
        }

        $attendanceStartTime = Carbon::parse($attendance->start_time);
        $maxStartTime = Carbon::parse($benefitConfig->working_day_start_max);

        if ($attendanceStartTime->gt($maxStartTime)) {
            return $attendanceStartTime->diffInMinutes($maxStartTime);
        }

        return 0; // Not late
    }

    /**
     * Get total late hours in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total hours late
     */
    public function getTotalLateHours($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $totalMinutes = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $lateMinutes = $this->getLateMinutesOnDate($currentDate);
            if ($lateMinutes !== null) {
                $totalMinutes += $lateMinutes;
            }
            $currentDate->addDay();
        }

        return $totalMinutes / 60.0; // Convert minutes to hours
    }

    /**
     * Get early departure minutes for a specific date
     *
     * @param Carbon|string $date The date to check
     * @return int|null Minutes left early or null if no attendance or no configuration or public holiday
     */
    public function getEarlyDepartureMinutesOnDate($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Check if this date is a public holiday - no penalty on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return null; // No penalty on public holidays
        }

        $attendance = $this->attendances()
            ->where('date', $date->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->first();

        if (!$attendance) {
            return null; // No attendance record
        }

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig || !$benefitConfig->working_day_end_min) {
            return null; // No benefit configuration or end time
        }

        $attendanceEndTime = Carbon::parse($attendance->end_time);
        $minEndTime = Carbon::parse($benefitConfig->working_day_end_min);

        if ($attendanceEndTime->lt($minEndTime)) {
            return $minEndTime->diffInMinutes($attendanceEndTime);
        }

        return 0; // Did not leave early
    }

    /**
     * Get total early departure hours in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total hours of early departures
     */
    public function getTotalEarlyDepartureHours($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $totalMinutes = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $earlyMinutes = $this->getEarlyDepartureMinutesOnDate($currentDate);
            if ($earlyMinutes !== null) {
                $totalMinutes += $earlyMinutes;
            }
            $currentDate->addDay();
        }

        return $totalMinutes / 60.0; // Convert minutes to hours
    }

    /**
     * Get total attendance penalty hours (combination of missed, late, early departure, and shortfall)
     * Enhanced to consider working time ranges from benefit configuration
     * Excludes public holidays from penalty calculations
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total hours to be penalized
     */
    public function getTotalPenaltyHours($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig) {
            return 0; // No benefit configuration
        }

        $dailyWorkingHours = $benefitConfig->daily_working_hours ?? 8;
        $workingDayStartMin = $benefitConfig->working_day_start_min;
        $workingDayStartMax = $benefitConfig->working_day_start_max;
        $workingDayEndMin = $benefitConfig->working_day_end_min;
        $workingDayEndMax = $benefitConfig->working_day_end_max;

        // Get public holidays in the date range
        $publicHolidays = PublicHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get missed working hours (full days with no attendance) - already excludes public holidays
        $missedHours = $this->getMissedWorkingHours($startDate, $endDate);

        // Get all attendance records for the period
        $attendances = $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->get();

        $totalPenaltyHours = $missedHours;

        // Process each attendance record to calculate penalty hours
        foreach ($attendances as $attendance) {
            // Skip penalty calculations for public holidays
            if (in_array($attendance->date, $publicHolidays)) {
                continue;
            }

            $penaltyHoursForDay = 0;

            // Skip if no start/end times
            if (!$attendance->start_time || !$attendance->end_time) {
                // If no times recorded, consider it as missing the full day
                $penaltyHoursForDay = $dailyWorkingHours;
            } else {
                $attendanceStart = Carbon::parse($attendance->date . ' ' . $attendance->start_time);
                $attendanceEnd = Carbon::parse($attendance->date . ' ' . $attendance->end_time);

                // Handle midnight crossover
                if ($attendanceEnd->lt($attendanceStart)) {
                    $attendanceEnd->addDay();
                }

                // Calculate valid working hours within the allowed time range
                $validWorkingHours = $this->calculateValidWorkingHours(
                    $attendanceStart,
                    $attendanceEnd,
                    $attendance->date,
                    $workingDayStartMin,
                    $workingDayStartMax,
                    $workingDayEndMin,
                    $workingDayEndMax
                );

                // Calculate penalty hours for this day
                $penaltyHoursForDay = max(0, $dailyWorkingHours - $validWorkingHours);
            }

            $totalPenaltyHours += $penaltyHoursForDay;
        }

        return $totalPenaltyHours;
    }

    /**
     * Calculate valid working hours within the allowed time range
     *
     * @param Carbon $attendanceStart Actual start time
     * @param Carbon $attendanceEnd Actual end time
     * @param string $date Date of attendance
     * @param string|null $workingDayStartMin Earliest allowed start time
     * @param string|null $workingDayStartMax Latest allowed start time
     * @param string|null $workingDayEndMin Earliest allowed end time
     * @param string|null $workingDayEndMax Latest allowed end time
     * @return float Valid working hours
     */
    private function calculateValidWorkingHours(
        Carbon $attendanceStart,
        Carbon $attendanceEnd,
        string $date,
        ?string $workingDayStartMin,
        ?string $workingDayStartMax,
        ?string $workingDayEndMin,
        ?string $workingDayEndMax
    ): float {
        // If no time constraints are set, use the actual hours worked
        if (!$workingDayStartMin || !$workingDayStartMax || !$workingDayEndMin || !$workingDayEndMax) {
            return $attendanceStart->diffInHours($attendanceEnd, true);
        }

        // Parse the allowed time ranges
        $allowedStartMin = Carbon::parse($date . ' ' . $workingDayStartMin);
        $allowedStartMax = Carbon::parse($date . ' ' . $workingDayStartMax);
        $allowedEndMin = Carbon::parse($date . ' ' . $workingDayEndMin);
        $allowedEndMax = Carbon::parse($date . ' ' . $workingDayEndMax);

        // Handle midnight crossover for end times
        if ($allowedEndMin->lt($allowedStartMin)) {
            $allowedEndMin->addDay();
        }
        if ($allowedEndMax->lt($allowedStartMax)) {
            $allowedEndMax->addDay();
        }
        if ($attendanceEnd->lt($attendanceStart)) {
            $attendanceEnd->addDay();
        }

        // Determine the effective working period within allowed ranges
        $effectiveStart = $attendanceStart;
        $effectiveEnd = $attendanceEnd;

        // Adjust start time if employee arrived too early or too late
        if ($attendanceStart->lt($allowedStartMin)) {
            // Arrived too early - start counting from allowed start min
            $effectiveStart = $allowedStartMin;
        } elseif ($attendanceStart->gt($allowedStartMax)) {
            // Arrived too late - start counting from actual arrival (penalty will be applied)
            $effectiveStart = $attendanceStart;
        }

        // Adjust end time if employee left too early or too late
        if ($attendanceEnd->lt($allowedEndMin)) {
            // Left too early - end counting at actual departure (penalty will be applied)
            $effectiveEnd = $attendanceEnd;
        } elseif ($attendanceEnd->gt($allowedEndMax)) {
            // Left too late - end counting at allowed end max (overtime not counted as penalty)
            $effectiveEnd = $allowedEndMax;
        }

        // Calculate valid hours, ensuring it's not negative
        $validHours = max(0, $effectiveStart->diffInHours($effectiveEnd, true));

        // Additional penalty for arriving late (after allowed start max)
        if ($attendanceStart->gt($allowedStartMax)) {
            $lateHours = $attendanceStart->diffInHours($allowedStartMax, true);
            $validHours = max(0, $validHours - $lateHours);
        }

        // Additional penalty for leaving early (before allowed end min)
        if ($attendanceEnd->lt($allowedEndMin)) {
            $earlyHours = $allowedEndMin->diffInHours($attendanceEnd, true);
            $validHours = max(0, $validHours - $earlyHours);
        }

        return $validHours;
    }

    /**
     * Calculate total penalty deduction based on all attendance issues
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param float $hourlyRate The hourly rate for deduction calculation
     * @return float Total deduction amount
     */
    public function calculateTotalPenaltyDeduction($startDate, $endDate, $hourlyRate = null)
    {
        if ($hourlyRate === null) {
            // Calculate hourly rate based on gross salary
            $grossSalary = $this->benefitConfiguration->gross_salary ?? 0;
            $workingDaysPerMonth = $this->workingDays->count() * 4; // Approximate
            $dailyHours = $this->benefitConfiguration->daily_working_hours ?? 8;

            $hourlyRate = $grossSalary / ($workingDaysPerMonth * $dailyHours);
        }

        $totalPenaltyHours = $this->getTotalPenaltyHours($startDate, $endDate);
        return $totalPenaltyHours * $hourlyRate;
    }

    /**
     * Get approved overtime hours in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return float Total approved overtime hours
     */
    public function getApprovedOvertimeHours($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        return $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->where('is_extra_hours_approved', true)
            ->sum('extra_hours');
    }

    /**
     * Calculate overtime pay for a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param float $overtimeRate The overtime rate multiplier (default: from benefit configuration)
     * @return float Overtime pay amount
     */
    public function calculateOvertimePay($startDate, $endDate, $overtimeRate = null)
    {
        if ($overtimeRate === null) {
            $overtimeRate = $this->benefitConfiguration->overtime_rate ?? 1.5;
        }

        // Calculate hourly rate based on gross salary
        $grossSalary = $this->benefitConfiguration->gross_salary ?? 0;
        $workingDaysPerMonth = $this->workingDays->count() * 4; // Approximate
        $dailyHours = $this->benefitConfiguration->daily_working_hours ?? 8;

        $hourlyRate = $grossSalary / ($workingDaysPerMonth * $dailyHours);
        $overtimeHours = $this->getApprovedOvertimeHours($startDate, $endDate);

        return $overtimeHours * $hourlyRate * $overtimeRate;
    }

    /**
     * Check if employee was late on a specific date
     *
     * @param Carbon|string $date The date to check
     * @return bool|null True if late, false if on time, null if no attendance or public holiday
     */
    public function wasLateOnDate($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Check if this date is a public holiday - no penalty on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return null; // No penalty on public holidays
        }

        $attendance = $this->attendances()
            ->where('date', $date->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->first();

        if (!$attendance) {
            return null; // No attendance record
        }

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig) {
            return null; // No benefit configuration
        }

        $startTimeLimit = $benefitConfig->working_day_start_max;
        if (!$startTimeLimit) {
            return null; // No start time constraint
        }

        $attendanceStartTime = Carbon::parse($attendance->start_time);
        $maxStartTime = Carbon::parse($startTimeLimit);

        return $attendanceStartTime->gt($maxStartTime);
    }

    /**
     * Count late days in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return int Number of days employee was late (excluding public holidays)
     */
    public function countLateDays($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig || !$benefitConfig->working_day_start_max) {
            return 0;
        }

        // Get public holidays in the date range
        $publicHolidays = PublicHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->toArray();

        return $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->where('start_time', '>', $benefitConfig->working_day_start_max)
            ->whereNotIn('date', $publicHolidays) // Exclude public holidays
            ->count();
    }

    /**
     * Check if employee left early on a specific date
     *
     * @param Carbon|string $date The date to check
     * @return bool|null True if left early, false if on time, null if no attendance or public holiday
     */
    public function leftEarlyOnDate($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Check if this date is a public holiday - no penalty on public holidays
        $isPublicHoliday = PublicHoliday::where('date', $date->format('Y-m-d'))->exists();
        if ($isPublicHoliday) {
            return null; // No penalty on public holidays
        }

        $attendance = $this->attendances()
            ->where('date', $date->format('Y-m-d'))
            ->where('is_approved', true)
            ->whereNull('payroll_id')
            ->first();

        if (!$attendance) {
            return null; // No attendance record
        }

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig) {
            return null; // No benefit configuration
        }

        $endTimeLimit = $benefitConfig->working_day_end_min;
        if (!$endTimeLimit) {
            return null; // No end time constraint
        }

        $attendanceEndTime = Carbon::parse($attendance->end_time);
        $minEndTime = Carbon::parse($endTimeLimit);

        return $attendanceEndTime->lt($minEndTime);
    }

    /**
     * Count early departure days in a date range
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @return int Number of days employee left early (excluding public holidays)
     */
    public function countEarlyDepartureDays($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $benefitConfig = $this->benefitConfiguration;
        if (!$benefitConfig || !$benefitConfig->working_day_end_min) {
            return 0;
        }

        // Get public holidays in the date range
        $publicHolidays = PublicHoliday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->toArray();

        return $this->attendances()
            ->where('date', '>=', $startDate->format('Y-m-d'))
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->where('is_approved', true)
            ->where('end_time', '<', $benefitConfig->working_day_end_min)
            ->whereNotIn('date', $publicHolidays) // Exclude public holidays
            ->count();
    }

    /**
     * Relation to attendance records
     */
    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance\Attendance::class);
    }

    public function activeEmployeeBaseBenefits($startDate)
    {
        return $this->baseBenefits()
            ->current($startDate)
            ->whereHas('packageDetail', function ($query) {
                $query->where('receiver', PackageDetail::RECEIVER_EMPLOYEE);
            });
    }

    public function activeOtherBaseBenefits($startDate)
    {
        return $this->baseBenefits()
            ->current($startDate)
            ->whereHas('packageDetail', function ($query) {
                $query->where('receiver', PackageDetail::RECEIVER_OTHER);
            });
    }

    public function extraPayments()
    {
        return $this->hasMany(\App\Models\Benefits\Payrolls\ExtraPayment::class);
    }

    public function getNegativeExtraPayments($startDate, $endDate): float
    {
        return $this->extraPayments()
            ->where('amount', '<', 0)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->where('status', \App\Models\Benefits\Payrolls\ExtraPayment::STATUS_APPROVED)
            ->sum('amount') ?? 0;
    }

    /**
     * Find employees eligible for payroll in a specific period
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $departmentIds Optional array of department IDs to filter by
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findForPayroll($startDate, $endDate, $departmentIds = [])
    {
        $query = self::where('status', self::STATUS_ACTIVE)
            ->whereNotNull('employment_date')
            ->where('employment_date', '<=', $endDate);

        if (!empty($departmentIds)) {
            $query->whereHas('position', function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            });
        }

        return $query->get();
    }

    /**
     * Get all payrolls this employee is part of
     */
    public function payrolls()
    {
        return $this->belongsToMany(\App\Models\Benefits\Payrolls\Payroll::class, 'payroll_employees');
    }

    // Method getPayrollDataForPeriod has been removed and the calculation logic
    // is now handled directly in the CreatePayroll component

    /**
     * Create benefit payment records for this employee's payroll from the passed base benefits
     *
     * @param \App\Models\Benefits\Payrolls\Payroll $payroll The payroll object
     * @param array $benefits Array containing 'benefits' collection of BaseBenefit models
     * @return array Array of created benefit payment IDs
     */
    public function createBenefitPaymentsForPayroll($payroll, $benefits)
    {
        $benefitPaymentIds = [];

        try {
            return DB::transaction(function () use ($payroll, $benefits, &$benefitPaymentIds) {
                // Create benefit payments using the benefits collection
                if (isset($benefits) && (is_array($benefits) || $benefits instanceof \Illuminate\Support\Collection)) {
                    foreach ($benefits as $baseBenefit) {
                        $amountRatio = 100;
                        $noOfDays = $payroll->start_date->diffInDays($payroll->end_date, true);
                        if ($baseBenefit->end_date) {
                            $amountRatio = $baseBenefit->end_date->diffInDays($payroll->start_date, true) / $noOfDays;
                        } else if ($baseBenefit->start_date->isAfter($payroll->start_date)) {
                            $amountRatio = $baseBenefit->start_date->diffInDays($payroll->start_date, true) / $noOfDays;
                        }
                        $amount = $baseBenefit->amount * ($amountRatio / 100);
                        $amount = $amount * days_coefficient($noOfDays, $baseBenefit->type);
                        $benefitPayment = BenefitPayment::create([
                            'employee_id' => $this->id,
                            'payroll_id' => $payroll->id,
                            'base_benefit_id' => $baseBenefit->id,
                            'amount' => $amount,
                            'status' => BenefitPayment::STATUS_PENDING,
                            'desc' => $baseBenefit->name ?? 'Benefit payment',
                        ]);
                        $benefitPaymentIds[] = $benefitPayment->id;
                    }
                }

                return $benefitPaymentIds;
            });
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error creating benefit payments', $e->getMessage(), loggable: $this);
            return $benefitPaymentIds;
        }
    }

    /**
     * Calculate penalty offset using vacation benefits and direct deduction
     * This method handles penalties by first using approved applied vacations,
     * then returns available vacation benefits for manual selection, and finally
     * calculates direct deduction for remaining penalties
     *
     * @param Carbon|string $startDate Start date of the range
     * @param Carbon|string $endDate End date of the range
     * @param float $hourlyRate The hourly rate for deduction calculation
     * @return array Array containing penalty breakdown and available vacation benefits
     */
    public function calculatePenaltyWithVacationOffset($startDate, $endDate, $hourlyRate = null)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        if ($hourlyRate === null) {
            // Calculate hourly rate based on gross salary
            $grossSalary = $this->benefitConfiguration->gross_salary ?? 0;
            $workingDaysPerMonth = $this->workingDays->count() * 4; // Approximate
            $dailyHours = $this->benefitConfiguration->daily_working_hours ?? 8;

            $hourlyRate = $grossSalary / ($workingDaysPerMonth * $dailyHours);
        }

        // Get total penalty hours
        $totalPenaltyHours = $this->getTotalPenaltyHours($startDate, $endDate);

        if ($totalPenaltyHours <= 0) {
            return [
                'total_penalty_hours' => 0,
                'vacation_offset_hours' => 0,
                'remaining_penalty_hours' => 0,
                'direct_deduction_amount' => 0,
                'used_approved_vacations' => [],
                'available_vacation_benefits' => []
            ];
        }

        $remainingPenaltyHours = $totalPenaltyHours;
        $vacationOffsetHours = 0;
        $usedApprovedVacations = [];

        // Step 1: Check for existing approved applied vacations in the period
        $approvedVacations = $this->appliedVacations()
            ->where('status', AppliedVacation::STATUS_APPROVED)
            ->whereHas('vacationDays', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('vacation_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->with('vacationDays')
            ->get();

        foreach ($approvedVacations as $appliedVacation) {
            if ($remainingPenaltyHours <= 0) break;

            $vacationHoursInPeriod = $appliedVacation->vacationDays()
                ->whereBetween('vacation_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('hours');

            $hoursToUse = min($remainingPenaltyHours, $vacationHoursInPeriod);

            if ($hoursToUse > 0) {
                $vacationOffsetHours += $hoursToUse;
                $remainingPenaltyHours -= $hoursToUse;

                $usedApprovedVacations[] = [
                    'applied_vacation_id' => $appliedVacation->id,
                    'vacation_benefit_name' => $appliedVacation->vacationBenefit->name ?? 'Unknown',
                    'hours_used' => $hoursToUse
                ];
            }
        }

        // Step 2: Get available vacation benefits for manual selection (if there are remaining penalty hours)
        $availableVacationBenefits = [];
        if ($remainingPenaltyHours > 0) {
            $vacationBenefits = $this->vacationBenefits()
                ->whereNull('end_date')
                ->where('current_balance', '>', 0)
                ->orderBy('current_balance', 'desc')
                ->get();

            foreach ($vacationBenefits as $vacationBenefit) {
                $availableVacationBenefits[] = [
                    'vacation_benefit_id' => $vacationBenefit->id,
                    'vacation_benefit_name' => $vacationBenefit->name,
                    'type' => $vacationBenefit->type,
                    'current_balance' => $vacationBenefit->current_balance,
                    'max_applicable_hours' => min($remainingPenaltyHours, $vacationBenefit->current_balance)
                ];
            }
        }

        // Step 3: Calculate direct deduction for remaining penalty hours
        $directDeductionAmount = $remainingPenaltyHours * $hourlyRate;

        return [
            'total_penalty_hours' => $totalPenaltyHours,
            'vacation_offset_hours' => $vacationOffsetHours,
            'remaining_penalty_hours' => $remainingPenaltyHours,
            'direct_deduction_amount' => $directDeductionAmount,
            'used_approved_vacations' => $usedApprovedVacations,
            'available_vacation_benefits' => $availableVacationBenefits
        ];
    }

    /**
     * Apply vacation benefit for penalty offset
     * This method creates a vacation application for the specified hours to offset penalties
     *
     * @param int $vacationBenefitId The vacation benefit ID to use
     * @param float $hours Hours to apply for vacation
     * @param Carbon|string $startDate Start date of the penalty period
     * @param Carbon|string $endDate End date of the penalty period
     * @return array Result of the vacation application
     */
    public function applyVacationForPenaltyOffset($vacationBenefitId, $hours, $startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        try {
            $vacationBenefit = $this->vacationBenefits()
                ->whereNull('end_date')
                ->where('id', $vacationBenefitId)
                ->first();

            if (!$vacationBenefit) {
                throw new \Exception('Vacation benefit not found or inactive');
            }

            if ($vacationBenefit->current_balance < $hours) {
                throw new \Exception('Insufficient vacation balance. Available: ' . $vacationBenefit->current_balance . ' hours, Requested: ' . $hours . ' hours');
            }

            // Generate vacation days for the penalty period
            $vacationDays = $this->generateVacationDaysForPenalty($startDate, $endDate, $hours);

            if (empty($vacationDays)) {
                throw new \Exception('No valid working days found in the penalty period for vacation application');
            }

            // Create the applied vacation record directly since applyForVacation doesn't return the object
            $appliedVacation = DB::transaction(function () use ($vacationBenefit, $hours, $vacationDays) {
                $appliedVacation = $this->appliedVacations()->create([
                    'vacation_benefit_id' => $vacationBenefit->id,
                    'hours' => $hours,
                    'new_balance' => $vacationBenefit->current_balance - $hours,
                    'status' => AppliedVacation::STATUS_APPROVED,
                    'admin_note' => 'Auto-created for penalty offset during payroll processing'
                ]);

                // Create vacation days
                if (count($vacationDays) > 0) {
                    $appliedVacation->vacationDays()->createMany($vacationDays);
                }

                // Update vacation benefit balance
                $vacationBenefit->update([
                    'current_balance' => $vacationBenefit->current_balance - $hours,
                ]);

                return $appliedVacation;
            });

            AppLog::info(
                'Manual Vacation Application for Penalty Offset',
                "Applied {$hours} hours of {$vacationBenefit->name} vacation for employee {$this->name} to offset penalties",
                loggable: $this
            );

            return [
                'success' => true,
                'applied_vacation_id' => $appliedVacation->id,
                'vacation_benefit_name' => $vacationBenefit->name,
                'hours_applied' => $hours,
                'vacation_days_count' => count($vacationDays),
                'message' => "Successfully applied {$hours} hours of {$vacationBenefit->name} vacation"
            ];
        } catch (\Exception $e) {
            AppLog::error(
                'Error applying vacation for penalty offset',
                "Failed to apply vacation for penalty offset: " . $e->getMessage(),
                loggable: $this
            );

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate vacation days for penalty offset
     * This method creates vacation day entries that correspond to the penalty period
     *
     * @param Carbon $startDate Start date of the penalty period
     * @param Carbon $endDate End date of the penalty period
     * @param float $totalHours Total hours to distribute across vacation days
     * @return array Array of vacation day data
     */
    private function generateVacationDaysForPenalty($startDate, $endDate, $totalHours)
    {
        $vacationDays = [];
        $remainingHours = $totalHours;
        $dailyWorkingHours = $this->benefitConfiguration?->daily_working_hours ?? 8;

        // Get working days configuration
        $workingDays = $this->workingDays->pluck('type')->map(function ($day) {
            return strtolower($day);
        })->toArray();

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate) && $remainingHours > 0) {
            $dayName = strtolower($currentDate->format('l'));

            // Only add vacation days for working days
            if (in_array($dayName, $workingDays)) {
                // Check if this is not a public holiday
                $isPublicHoliday = \App\Models\Attendance\PublicHoliday::where('date', $currentDate->format('Y-m-d'))->exists();

                if (!$isPublicHoliday) {
                    $hoursForDay = min($remainingHours, $dailyWorkingHours);

                    $vacationDays[] = [
                        'vacation_date' => $currentDate->format('Y-m-d'),
                        'hours' => $hoursForDay
                    ];

                    $remainingHours -= $hoursForDay;
                }
            }

            $currentDate->addDay();
        }

        return $vacationDays;
    }
}
