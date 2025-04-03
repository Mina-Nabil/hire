<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use Database\Factories\DepartmentFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory;
    
    const MORPH_NAME = 'department';

    protected $fillable = [
        'prefix_code',
        'name',
        'desc',
    ];

    /**
     * Get all positions that belong to this department.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Create a new department
     * 
     * @param string $name
     * @param string $prefix_code
     * @param string|null $description
     * @return Department
     */
    public static function createDepartment(string $name, string $prefix_code, ?string $description = null): Department
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('create', Department::class)) {
            throw new AppException('You are not authorized to create a department');
        }

        try {
            return self::create([
                'name' => $name,
                'prefix_code' => $prefix_code,
                'desc' => $description,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create department');
        }
    }


    ////model methods
    public function editInfo(string $name, string $prefix_code, ?string $description = null): bool
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('update', $this)) {
            throw new AppException('You are not authorized to edit this department');
        }

        try {
            return $this->update([
                'name' => $name,
                'prefix_code' => $prefix_code,
                'desc' => $description,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to edit department');
        }
    }


    public function deleteDepartment()
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('delete', $this)) {
            throw new AppException('You are not authorized to delete this department');
        }
        // Check if there are any positions associated with this department
        if ($this->positions()->count() > 0) {
            throw new AppException('Cannot delete department with associated positions.');
        } else {
            $this->delete();
        }
    }



    ///scope methods
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('prefix_code', 'like', '%' . $search . '%');
    }


    public static function newFactory()
    {
        return DepartmentFactory::new();
    }
}
