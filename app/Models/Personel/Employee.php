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

    // Document status constants
    const DOC_STATUS_VALID = 'valid';
    const DOC_STATUS_NEAR_EXPIRY = 'near_expiry';
    const DOC_STATUS_EXPIRED = 'expired';
    const DOC_STATUS_MISSING = 'missing';

    // Default days threshold for near expiry warning (7 days)
    const NEAR_EXPIRY_DAYS = 7;

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
            return true;
        } catch (Exception $e) {
            report($e);
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
            return true;
        } catch (Exception $e) {
            report($e);
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
                ->orWhereDoesntHave('employeeS6Doc');
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
                // 2. Birth certificate expired
                ->orWhereHas('birthCertificate', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 3. Contract expired
                ->orWhereHas('contracts', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 4. Army service paper expired
                ->orWhereHas('armyServicePaper', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 5. Driver license expired
                ->orWhereHas('driverLicense', function ($q) use ($today) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', $today);
                })
                // 6. Police records expired
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
                });
        });
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

        return $expiredDocs;
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
     * @return string Document status
     */
    public function checkMedicalRecordStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        return $this->checkDocumentStatus($this->medicalRecord, $nearExpiryDays);
    }

    /**
     * Check external medical record status
     *
     * @param int $nearExpiryDays Days threshold for near expiry warning
     * @return string Document status
     */
    public function checkExternalMedicalRecordStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
    {
        return $this->checkDocumentStatus($this->externalMedicalRecord, $nearExpiryDays);
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
    public function checkWorkDeclarationsStatus($nearExpiryDays = self::NEAR_EXPIRY_DAYS)
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
}
