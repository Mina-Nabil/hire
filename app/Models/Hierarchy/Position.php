<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Users\AppLog;
use App\Scopes\HrLocationScope;
use Database\Factories\PositionFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Position extends Model
{
    use HasFactory;
    
    const MORPH_NAME = 'position';

    protected $fillable = [
        'location_id',
        'department_id',
        'code',
        'name',
        'arabic_name',
        'job_description',
        'arabic_job_description',
        'job_requirements',
        'arabic_job_requirements',
        'job_qualifications',
        'arabic_job_qualifications',
        'job_benefits',
        'arabic_job_benefits',
        'employee_id',
        'salary_grade_id',
        'parent_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        return static::addGlobalScope(new HrLocationScope);
    }



    /**
     * Get the department that this position belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the location that this position belongs to.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    /**
     * Get the employee assigned to this position.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the salary grade assigned to this position.
     */
    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    /**
     * Get the parent position.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    /**
     * Get the child positions.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Position::class, 'parent_id');
    }

    /**
     * Get the vacancies for this position.
     */
    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    ////static methods

    /**
     * Create a new position
     * 
     * @param int $departmentId
     * @param string $name
     * @param string $arabicName
     * @param array $attributes Additional attributes for the position
     * @return Position
     */
    public static function createPosition(
        int $locationId,
        int $departmentId,
        string $name,
        string $arabicName ,
        ?int $parentId = null,
        ?string $jobDescription = null,
        ?string $arabicJobDescription = null,
        ?string $jobRequirements = null,
        ?string $arabicJobRequirements = null,
        ?string $jobQualifications = null,
        ?string $arabicJobQualifications = null,
        ?string $jobBenefits = null,
        ?string $arabicJobBenefits = null,
        ?string $code = null,
        ?string $employeeId = null,
        ?int $salaryGradeId = null,
    ): Position {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('create', Position::class)) {
            throw new AppException('You are not authorized to create a position');
        }

        try {
            $newPosition = self::create([
                'location_id' => $locationId,
                'department_id' => $departmentId,
                'name' => $name,
                'arabic_name' => $arabicName,
                'job_description' => $jobDescription,
                'arabic_job_description' => $arabicJobDescription,
                'job_requirements' => $jobRequirements,
                'arabic_job_requirements' => $arabicJobRequirements,
                'job_qualifications' => $jobQualifications,
                'arabic_job_qualifications' => $arabicJobQualifications,
                'job_benefits' => $jobBenefits,
                'arabic_job_benefits' => $arabicJobBenefits,
                'parent_id' => $parentId,
                'code' => $code,
                'employee_id' => $employeeId,
                'salary_grade_id' => $salaryGradeId,
            ]);
            AppLog::info('Position Created', "Name: $name", loggable: $newPosition);
            return $newPosition;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating position', $e->getMessage());
            throw new AppException('Failed to create position');
        }
    }

    ////model methods


    public function editInfo(
        int $locationId,
        int $departmentId,
        string $name,
        $parentId,
        string $arabicName,
        ?string $jobDescription,
        ?string $arabicJobDescription,
        ?string $jobRequirements,
        ?string $arabicJobRequirements,
        ?string $jobQualifications,
        ?string $arabicJobQualifications,
        ?string $jobBenefits,
        ?string $arabicJobBenefits,
        ?string $code,
        ?string $employeeId,
        $salaryGradeId,
    ): bool {
        try {
            /** @var User $loggerInUser */
            $loggerInUser = Auth::user();
            if (!$loggerInUser->can('update', $this)) {
                throw new AppException('You are not authorized to edit this position');
            }

            DB::beginTransaction();
            $this->update([
                'location_id' => $locationId,
                'department_id' => $departmentId,
                'name' => $name,
                'arabic_name' => $arabicName,
                'job_description' => $jobDescription,
                'arabic_job_description' => $arabicJobDescription,
                'job_requirements' => $jobRequirements,
                'arabic_job_requirements' => $arabicJobRequirements,
                'job_qualifications' => $jobQualifications,
                'arabic_job_qualifications' => $arabicJobQualifications,
                'job_benefits' => $jobBenefits,
                'arabic_job_benefits' => $arabicJobBenefits,
                'parent_id' => $parentId,
                'code' => $code
            ]);
            if($employeeId) {
                $this->employee()->associate($employeeId);
                $this->save();
            } else {
                $this->employee()->dissociate();
                $this->save();
            }
            if($salaryGradeId) {
                $this->salaryGrade()->associate($salaryGradeId);
                $this->save();
            } else {
                $this->salaryGrade()->dissociate();
                $this->save();
            }
            DB::commit();
            AppLog::info('Position Updated', "Name: $name", loggable: $this);
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing position', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to edit position');
        }
    }

    public function deletePosition()
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('delete', $this)) {
            throw new AppException('You are not authorized to delete this position');
        }

         // Check if there are child positions
         if ($this->children()->count() > 0) {
            throw new AppException('Cannot delete position with child positions. Please reassign or delete child positions first.');
        } 
        // Check if there is an employee assigned to this position
        else if ($this->employee()->exists()) {
            throw new AppException('Cannot delete position with an assigned employee.');
        }
        // Check if there are vacancies for this position
        else if ($this->vacancies()->count() > 0) {
            throw new AppException('Cannot delete position with vacancies.');
        }

        try {
            $this->delete();
            AppLog::info('Position Deleted', "Name: $this->name");
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting position', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to delete position');
        }
    }
    /**
     * Get position hierarchy level
     * 
     * @return int The level of the position in the hierarchy (0 = top level)
     */
    public function getHierarchyLevel(): int
    {
        $level = 0;
        $current = $this;

        while ($current->parent_id) {
            $level++;
            $current = $current->parent;
        }

        return $level;
    }

    public function getPotentialManagersAttribute(): array
    {
        $potentialManagers = [];
        $current = $this;
        while ($current->parent_id) {
            if($current->parent->employee_id) {                                 
                $potentialManagers[] = $current->parent->employee;
            }
            $current = $current->parent;
        }
        return $potentialManagers;
    }

    /**
     * Check if this position is a manager position 
     * (has child positions)
     * 
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->children()->exists();
    }


    ///scope methods
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('arabic_name', 'like', '%' . $search . '%')
            ->orWhereHas('department', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
    }

    public function scopeAvailableForRecruitment($query)
    {
        return $query->where(function ($query) {
            $query->whereHas('vacancies', function ($q) {
                    $q->where('status', Vacancy::STATUS_CLOSED);
                })
                ->orWhereDoesntHave('vacancies');
        })->whereNull('employee_id');
    }


    public static function newFactory()
    {
        return PositionFactory::new();
    }
}
