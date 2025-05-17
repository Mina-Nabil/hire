<?php

namespace App\Models\Benefits\Configurations;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryGrade extends Model
{
    const MORPH_NAME = 'salary_grade';

    protected $table = 'salary_grades';
    protected $fillable = [
        'name',
        'desc',
        'gross_min',
        'gross_max',
    ];

    public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class);
    }

    public function baseBenefits()
    {
        return $this->hasMany(BaseBenefit::class);
    }

    public function benefitConfigurations()
    {
        return $this->hasMany(BenefitConfiguration::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, BenefitConfiguration::class);
    }

    ///static functions

    /**
     * @param string $name
     * @param string $desc
     * @param float $grossMin
     * @param float $grossMax
     * @param array $packageDetails
     * [
     *  [
     *      'name' => 'name',
     *      'receiver' => 'receiver',
     *      'type' => 'type',
     *      'amount_min' => 'amount_min',
     *      'amount_max' => 'amount_max',
     *      'is_hidden' => 'is_hidden',
     *  ]
     * ]
     * @return SalaryGrade
     */
    public static function createSalaryGrade(string $name, float $grossMin, float $grossMax, array $packageDetails = [], ?string $desc = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('create', SalaryGrade::class)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $desc, $grossMin, $grossMax, $packageDetails) {

                $package = new SalaryGrade([
                    'name' => $name,
                    'gross_min' => $grossMin,
                    'gross_max' => $grossMax,
                    'desc' => $desc,
                ]);
                $package->save();

                $package->packageDetails()->createMany($packageDetails);

                AppLog::info('Salary Grade Created', "Name: $name", loggable: $package);
                return $package;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating salary grade', $e->getMessage());
            throw new AppException('Failed to create benefit package');
        }
    }

    ///model functions

    /**
     * @param string $name
     * @param string $desc
     * @param float $grossMin
     * @param float $grossMax
     * @param array $packageDetails
     * [
     *  [
     *      'id' => 'id',
     *      'name' => 'name',
     *      'type' => 'type',
     *      'amount_min' => 'amount_min',
     *      'amount_max' => 'amount_max',
     *      'receiver' => 'receiver',
     *      'is_hidden' => 'is_hidden',
     *  ]
     * ]
     * @return void
     */
    public function editPackage(string $name, float $grossMin, float $grossMax, array $packageDetails = [], ?string $desc = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $grossMin, $grossMax, $desc, $packageDetails) {
                $this->name = $name;
                $this->gross_min = $grossMin;
                $this->gross_max = $grossMax;
                $this->desc = $desc;
                $this->save();

                $this->packageDetails()->delete();

                $this->packageDetails()->createMany($packageDetails);

                AppLog::info('Salary Grade Updated', "Name: $name", loggable: $this);
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing salary grade', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to edit benefit package');
        }
    }


    public function deletePackage()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException('Unauthorized');
        }

        if ($this->benefitConfigurations()->exists()) {
            throw new AppException('Cannot delete package linked to an employee');
        }
        try {
            $this->delete();
            AppLog::info('Salary Grade Deleted', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting salary grade', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to delete benefit package');
        }
    }
}
