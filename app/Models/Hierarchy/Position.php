<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Vacancies\Vacancy;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    const MORPH_NAME = 'position';
    
    protected $fillable = [
        'department_id',
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
        string $arabicName,
        ?string $jobDescription,
        ?string $arabicJobDescription,
        ?string $jobRequirements,
        ?string $arabicJobRequirements,
        ?string $jobQualifications,
        ?string $arabicJobQualifications,
        ?string $jobBenefits,
        ?string $arabicJobBenefits,
    ): Position {
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
        string $arabicName,
        ?string $jobDescription,
        ?string $arabicJobDescription,
        ?string $jobRequirements,
        ?string $arabicJobRequirements,
        ?string $jobQualifications,
        ?string $arabicJobQualifications,
        ?string $jobBenefits,
        ?string $arabicJobBenefits,
    ): bool {
        try {
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
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to edit position');
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
}
