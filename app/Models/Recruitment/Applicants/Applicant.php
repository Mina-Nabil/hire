<?php

namespace App\Models\Recruitment\Applicants;

use App\Exceptions\AppException;
use App\Models\Base\Area;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Users\Document;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Applicant extends Model
{
    const MORPH_NAME = 'applicant';
    const CV_PATH = 'applicant-documents';
    const IMAGE_PATH = 'applicant-images';

    protected $table = 'applicants';
    protected $fillable = [
        'area_id',
        'first_name',
        'middle_name',
        'last_name',
        'nationality',
        'email',
        'phone',
        'address',
        'social_number',
        'home_phone',
        'birth_date',
        'gender',
        'marital_status',
        'military_status',
        'image_url',
        'cv_url',
        'signature_url',
        'signature_date',
        'is_hired'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'signature_date' => 'datetime',
    ];

    //constants
    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';
    const GENDER = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    const MARITAL_STATUS_SINGLE = 'Single';
    const MARITAL_STATUS_MARRIED = 'Married';
    const MARITAL_STATUS_DIVORCED = 'Divorced';
    const MARITAL_STATUS_WIDOWER = 'Widowed';
    const MARITAL_STATUS = [
      self::MARITAL_STATUS_SINGLE, 
      self::MARITAL_STATUS_MARRIED,
      self::MARITAL_STATUS_DIVORCED,
      self::MARITAL_STATUS_WIDOWER,
    ];  

    const MILITARY_STATUS_EXEMPTED = 'Exempted';
    const MILITARY_STATUS_DRAFTED = 'Drafted';
    const MILITARY_STATUS_COMPLETED = 'Completed';
    const MILITARY_STATUS = [
        self::MILITARY_STATUS_EXEMPTED,
        self::MILITARY_STATUS_DRAFTED,
        self::MILITARY_STATUS_COMPLETED,
    ];

    ///attributes
    /**
     * Get the full name of the applicant
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

    public function getFullCvUrlAttribute(): string
    {
        return $this->cv_url ? Storage::disk('s3')->url($this->cv_url) : null;
    }

    public function getFullImageUrlAttribute(): string
    {
        return $this->image_url ? Storage::disk('s3')->url($this->image_url) : null;
    }

    ////static methods



    ///relations
    /**
     * Get the area that the applicant belongs to
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get all applications for this applicant
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all education records for this applicant
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Get all training records for this applicant
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    /**
     * Get all experience records for this applicant
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * Get all language records for this applicant
     */
    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }

    /**
     * Get all interviews for this applicant
     */
    public function interviews(): HasManyThrough
    {
        return $this->hasManyThrough(Interview::class, Application::class);
    }

    /**
     * Get all reference records for this applicant
     */
    public function references(): HasMany
    {
        return $this->hasMany(Reference::class);
    }

    /**
     * Get all skill records for this applicant
     */
    public function skills(): HasMany
    {
        return $this->hasMany(ApplicantSkill::class);
    }

    /**
     * Get the health record for this applicant
     */
    public function health(): HasOne
    {
        return $this->hasOne(ApplicantHealth::class);
    }
    
    /**
     * Get all documents for this applicant
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'doc_owner');
    }

    /**
     * Create a new applicant
     * 
     * @param int $areaId
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     * @param array $additionalData
     * @return Applicant
     */
    public static function createApplicant(
        int $areaId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $socialNumber,
        array $additionalData = []
    ): Applicant {
        try {
            return DB::transaction(function () use ($areaId, $firstName, $lastName, $email, $phone, $socialNumber, $additionalData) {
                return self::updateOrCreate([
                    'socialNumber' => $socialNumber,
                ], array_merge([
                    'area_id' => $areaId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'email' =>  $email
                ], $additionalData));
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create applicant: ' . $e->getMessage());
        }
    }

    /**
     * Update the applicant's personal information
     * 
     * @param array $data
     * @return bool
     */
    public function updatePersonalInfo(array $data): bool
    {
        try {
            return $this->update($data);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to update applicant information: ' . $e->getMessage());
        }
    }


    /**
     * Update the applicant's CV
     * 
     * @param string $cvUrl
     * @return Applicant
     */
    public function updateCv(string $cvUrl): bool
    {
        try {
            if($this->cv_url){
                Storage::disk('s3')->delete($this->cv_url);
            }
            $this->cv_url = $cvUrl;
            return $this->save();
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to update CV: ' . $e->getMessage());
        }
    }
    /**
     * Set education records for this applicant
     * This will remove existing education records and create new ones
     * 
     * @param array $educations Array of education data
     * @return Applicant
     */
    public function setEducations(array $educations): Applicant
    {
        try {
            return DB::transaction(function () use ($educations) {
                // Delete existing educations
                $this->educations()->delete();

                // Create new educations
                foreach ($educations as $education) {
                    $this->educations()->create($education);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set education records: ' . $e->getMessage());
        }
    }

    /**
     * Set training records for this applicant
     * This will remove existing training records and create new ones
     * 
     * @param array $trainings Array of training data
     * @return Applicant
     */
    public function setTrainings(array $trainings): Applicant
    {
        try {
            return DB::transaction(function () use ($trainings) {
                // Delete existing trainings
                $this->trainings()->delete();

                // Create new trainings
                foreach ($trainings as $training) {
                    $this->trainings()->create($training);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set training records: ' . $e->getMessage());
        }
    }

    /**
     * Set experience records for this applicant
     * This will remove existing experience records and create new ones
     * 
     * @param array $experiences Array of experience data
     * @return Applicant
     */
    public function setExperiences(array $experiences): Applicant
    {
        try {
            return DB::transaction(function () use ($experiences) {
                // Delete existing experiences
                $this->experiences()->delete();

                // Create new experiences
                foreach ($experiences as $experience) {
                    $this->experiences()->create($experience);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set experience records: ' . $e->getMessage());
        }
    }

    /**
     * Set language records for this applicant
     * This will remove existing language records and create new ones
     * 
     * @param array $languages Array of language data
     * @return Applicant
     */
    public function setLanguages(array $languages): Applicant
    {
        try {
            return DB::transaction(function () use ($languages) {
                // Delete existing languages
                $this->languages()->delete();

                // Create new languages
                foreach ($languages as $language) {
                    $this->languages()->create($language);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set language records: ' . $e->getMessage());
        }
    }

    /**
     * Set reference records for this applicant
     * This will remove existing reference records and create new ones
     * 
     * @param array $references Array of reference data
     * @return Applicant
     */
    public function setReferences(array $references): Applicant
    {
        try {
            return DB::transaction(function () use ($references) {
                // Delete existing references
                $this->references()->delete();

                // Create new references
                foreach ($references as $reference) {
                    $this->references()->create($reference);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set reference records: ' . $e->getMessage());
        }
    }

    /**
     * Set skill records for this applicant
     * This will remove existing skill records and create new ones
     * 
     * @param array $skills Array of skill data
     * @return Applicant
     */
    public function setSkills(array $skills): Applicant
    {
        try {
            return DB::transaction(function () use ($skills) {
                // Delete existing skills
                $this->skills()->delete();

                // Create new skills
                foreach ($skills as $skill) {
                    $this->skills()->create($skill);
                }

                return $this;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set skill records: ' . $e->getMessage());
        }
    }

    /**
     * Set health record for this applicant
     * This will update or create the health record
     * 
     * @param bool $hasHealthIssues
     * @param string|null $healthIssues
     * @return ApplicantHealth
     */
    public function setHealth(bool $hasHealthIssues, ?string $healthIssues = null): ApplicantHealth
    {
        try {
            return DB::transaction(function () use ($hasHealthIssues, $healthIssues) {
                return $this->health()->updateOrCreate(
                    ['applicant_id' => $this->id],
                    [
                        'has_health_issues' => $hasHealthIssues,
                        'health_issues' => $healthIssues,
                    ]
                );
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set health record: ' . $e->getMessage());
        }
    }

    /**
     * Upload and set the applicant's profile image
     * 
     * @param string $imageUrl
     * @return bool
     */
    public function setImage(string $imageUrl): bool
    {
        try {
            return $this->update(['image_url' => $imageUrl]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set profile image: ' . $e->getMessage());
        }
    }

    /**
     * Set the applicant's signature and signature date
     * 
     * @param string $signatureUrl
     * @return bool
     */
    public function setSignature(string $signatureUrl): bool
    {
        try {
            return $this->update([
                'signature_url' => $signatureUrl,
                'signature_date' => now(),
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to set signature: ' . $e->getMessage());
        }
    }

    /**
     * Apply for a vacancy
     * 
     * @param int $vacancyId
     * @param string|null $coverLetter
     * @return Application
     */
    public function applyForVacancy(int $vacancyId, ?string $coverLetter = null, ?int $refered_by_id = null): Application
    {
        try {
            return DB::transaction(function () use ($vacancyId, $coverLetter, $refered_by_id) {
                return $this->applications()->create([
                    'vacancy_id' => $vacancyId,
                    'cover_letter' => $coverLetter,
                    'status' => Application::STATUS_PENDING,
                    'referred_by_id' => $refered_by_id,
                ]);
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to apply for vacancy: ' . $e->getMessage());
        }
    }

    /**
     * Hire the applicant
     * 
     * @return bool
     */
    public function hire(): bool
    {
        return $this->update(['is_hired' => true]);
    }

    /**
     * Scope to search applicants by name or position applied
     */
    public function scopeSearch($query, $search)
    {
        $texts = explode(' ', $search);
        foreach ($texts as $text) {
            $query->where(function ($query) use ($text) {
                $query->where('first_name', 'like', "%{$text}%")
                    ->orWhere('middle_name', 'like', "%{$text}%")
                    ->orWhere('last_name', 'like', "%{$text}%")
                    ->orWhereHas('applications.vacancy.position', function ($q) use ($text) {
                        $q->where('name', 'like', "%{$text}%");
                    });
            });
        }
        return $query;
    }

    /**
     * Scope to filter applicants created from a specific date
     */
    public function scopeCreatedFrom($query, $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * Scope to filter applicants created to a specific date
     */
    public function scopeCreatedTo($query, $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    /**
     * Scope to filter applicants by military status
     */
    public function scopeWithMilitaryStatus($query, $status)
    {
        return $query->where('military_status', $status);
    }

    /**
     * Scope to filter applicants by marital status
     */
    public function scopeWithMaritalStatus($query, $status)
    {
        return $query->where('marital_status', $status);
    }

    /**
     * Scope to filter applicants by area
     */
    public function scopeFromCity($query, $cityId)
    {
        return $query->whereHas('area', function ($q) use ($cityId) {
            $q->where('city_id', $cityId);
        });
    }

    /**
     * Scope to filter applicants by area
     */
    public function scopeFromArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    /**
     * Scope to filter applicants older than a specific age
     */
    public function scopeOlderThan($query, $years)
    {
        $date = now()->subYears($years)->format('Y-m-d');
        return $query->whereDate('birth_date', '<=', $date);
    }

    /**
     * Scope to filter applicants younger than a specific age
     */
    public function scopeYoungerThan($query, $years)
    {
        $date = now()->subYears($years)->format('Y-m-d');
        return $query->whereDate('birth_date', '>=', $date);
    }

    /**
     * Scope to filter applicants by vacancy ID
     */
    public function scopeByVacancyId($query, int $vacancyId)
    {
        return $query->whereHas('applications', function ($q) use ($vacancyId) {
            $q->where('vacancy_id', $vacancyId);
        });
    }
}
