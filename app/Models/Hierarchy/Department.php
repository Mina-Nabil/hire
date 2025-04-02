<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
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
}
