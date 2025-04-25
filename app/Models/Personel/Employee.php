<?php

namespace App\Models\Personel;

use App\Exceptions\AppException;
use App\Models\Base\City;
use App\Models\Base\InsuranceOffice;
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

class Employee extends Model
{
    const MORPH_NAME = 'employee';

    const FILES_DIRECTORY = 'employees';

    protected $fillable = ['name', 'email', 'phone', 'address', 'nationality', 'gender', 'birth_date', 'image_url', 'birth_place_id', 'license_required', 'employment_date'];

    protected $casts = [
        'employment_date' => 'date',
        'birth_date' => 'date',
    ];

    ////model functions
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
            return true;
        } catch (Exception $e) {
            report($e);
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
            return true;
        } catch (Exception $e) {
            report($e);
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
            return true;
        } catch (Exception $e) {
            report($e);
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
            return true;
        } catch (Exception $e) {
            report($e);
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
            $this->employeeContract()->updateOrCreate(
                [
                    'issue_date' => $issue_date,
                ],
                [
                    'created_by' => $loggedInUser->id,
                    'file_path' => $file_path,
                    'expiry_date' => $expiry_date,
                ],
            );
            return true;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error setting employee contract');
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
            return true;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error setting employee S1 doc');
        }
    }

    public function setEmployeeS2Doc($file_path, Carbon $issue_date, Carbon $expiry_date, float $s2_amount, int $year)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            // Create a new record instead of updating existing one to support multiple S2 docs
            $this->employeeS2Doc()->create([
                'created_by' => $loggedInUser->id,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                's2_amount' => $s2_amount,
                'year' => $year,
            ]);
            return true;
        } catch (Exception $e) {
            report($e);
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
            $doc = $this->employeeS6Doc()->create([
                'created_by' => $loggedInUser->id,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                's6_number' => $s6_number,
                'leaving_reason' => $leaving_reason,
            ]);
            return true;
        } catch (Exception $e) {
            report($e);
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
            return true;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error setting police record');
        }
    }

    public function setHrLetter($file_path, Carbon $issue_date, Carbon $expiry_date)
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
            return true;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error setting hr letter');
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
    public function updateBaseInfo(string $name, string $email, string $phone, string $address, string $nationality, string $gender, $birth_date, $employment_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You do not have permission to update this employee');
        }

        try {
            $this->update([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'nationality' => $nationality,
                'gender' => $gender,
                'birth_date' => $birth_date,
                'employment_date' => $employment_date,
            ]);

            return $this->fresh();
        } catch (Exception $e) {
            report($e);
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
    public function updateEmployeeInfo(int $insurance_office_id, ?string $insurance_number = null, ?string $insurance_amount = null, ?string $academic_qualification = null, ?string $university = null, ?int $graduation_year = null, ?string $military_status = null, ?string $marital_status = null)
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

            return $employeeInfo;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error updating employee information: ' . $e->getMessage());
        }
    }

    //scopes
    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where(function ($query) use ($now) {
            $query->whereNull('termination_date');
        });
    }

    ////relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function info()
    {
        return $this->hasOne(EmployeeInfo::class);
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
}
