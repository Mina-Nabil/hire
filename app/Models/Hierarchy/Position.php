<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Vacancies\Vacancy;
use Database\Factories\PositionFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Position extends Model
{
    use HasFactory;
    
    const MORPH_NAME = 'position';

    protected $fillable = [
        'department_id',
        'code',
        'sap_code',
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
        'parent_id',
    ];

    /**
     * Get the department that this position belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee assigned to this position.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
        int $departmentId,
        string $name,
        ?int $parentId,
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
        ?string $sapCode,
    ): Position {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('create', Position::class)) {
            throw new AppException('You are not authorized to create a position');
        }

        try {
            $newPosition = self::create([
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
                'sap_code' => $sapCode,
            ]);
            return $newPosition;
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create position');
        }
    }

    ////model methods


    public function editInfo(
        int $departmentId,
        string $name,
        ?int $parentId,
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
        ?string $sapCode,
    ): bool {
        try {
            /** @var User $loggerInUser */
            $loggerInUser = Auth::user();
            if (!$loggerInUser->can('update', $this)) {
                throw new AppException('You are not authorized to edit this position');
            }


            return $this->update([
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
                'sap_code' => $sapCode,
            ]);
        } catch (Exception $e) {
            report($e);
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
            throw new AppException('Cannot delete position with active vacancies.');
        }

        try {
            $this->delete();
        } catch (Exception $e) {
            report($e);
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
            });
    }


    public static function newFactory()
    {
        return PositionFactory::new();
    }
}
