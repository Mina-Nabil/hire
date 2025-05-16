<?php

namespace App\Models\Benefits\Configurations;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VacationPackage extends Model
{
    const MORPH_NAME = 'vacation_package';

    protected $table = 'vacation_packages';
    protected $fillable = [
        'name',
        'desc',
    ];

    public function vacationDetails()
    {
        return $this->hasMany(VacationDetail::class);
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
     * @param array $vacationDetails
     * [
     *  [
     *      'name' => 'name',
     *      'type' => 'type',
     *      'inc_rate_min' => 'inc_rate_min',
     *      'inc_rate_max' => 'inc_rate_max',
     *      'max_balance' => 'max_balance',
     *      'hour_price' => 'hour_price',
     *  ]
     * ]
     * @return VacationPackage
     */
    public static function createVacationPackage($name, $desc, $vacationDetails)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if ($loggedInUser && !$loggedInUser->can('create', self::class)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $desc, $vacationDetails) {

                $package = new VacationPackage([
                    'name' => $name,
                    'desc' => $desc,
                ]);
                $package->save();

                $package->vacationDetails()->createMany($vacationDetails);

                AppLog::info('Vacation Package Created', "Name: $name", loggable: $package);
                return $package;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating vacation package', $e->getMessage());
            throw new AppException('Failed to create benefit package');
        }
    }

    ///model functions

    /**
     * @param string $name
    * @param string $desc
     * @param array $vacationDetails
     * [
     *  [
     *      'id' => 'id',
     *      'name' => 'name',
     *      'type' => 'type',
     *      'receiver' => 'receiver',
     *      'inc_rate_min' => 'inc_rate_min',
     *      'inc_rate_max' => 'inc_rate_max',
     *      'max_balance_min' => 'max_balance_min',
     *      'max_balance_max' => 'max_balance_max',
     *      'hour_price_min' => 'hour_price_min',
     *      'hour_price_max' => 'hour_price_max',
     *  ]
     * ]
     * @return void
     */
    public function editPackage($name, $desc, $vacationDetails)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $desc, $vacationDetails) {
                $this->name = $name;
                $this->desc = $desc;
                $this->save();

                $this->vacationDetails()->delete();

                $this->vacationDetails()->createMany($vacationDetails);
            });
            AppLog::info('Vacation Package Updated', "Name: $name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing vacation package', $e->getMessage(), loggable: $this);
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
            AppLog::info('Vacation Package Deleted', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting vacation package', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to delete benefit package');
        }
    }
}
