<?php

namespace App\Models\Benefits\Configurations;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Vacations\VacationDetail;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BenefitPackage extends Model
{
    const MORPH_NAME = 'benefit_package';

    protected $table = 'benefit_packages';
    protected $fillable = [
        'name',
        'desc',
    ];

    public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class);
    }

    public function baseBenefits()
    {
        return $this->hasMany(BaseBenefit::class);
    }

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
     * @param array $packageDetails
     * [
     *  [
     *      'name' => 'name',
     *      'receiver' => 'receiver',
     *      'type' => 'type',
     *      'amount_min' => 'amount_min',
     *      'amount_max' => 'amount_max',
     *      'is_net' => 'is_net',
     *      'is_gross' => 'is_gross',
     *      'is_grand_gross' => 'is_grand_gross',
     *      'is_hidden' => 'is_hidden',
     *  ]
     * ]
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
     * @return BenefitPackage
     */
    public static function createPackage($name, $desc, $packageDetails, $vacationDetails)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', BenefitPackage::class)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $desc, $packageDetails, $vacationDetails) {

                $package = new BenefitPackage([
                    'name' => $name,
                    'desc' => $desc,
                ]);
                $package->save();

                $package->packageDetails()->createMany($packageDetails);
                $package->vacationDetails()->createMany($vacationDetails);

                return $package;
            });
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to create benefit package');
        }
    }

    ///model functions

    /**
     * @param string $name
     * @param string $desc
     * @param array $packageDetails
     * [
     *  [
     *      'id' => 'id',
     *      'name' => 'name',
     *      'type' => 'type',
     *      'amount_min' => 'amount_min',
     *      'amount_max' => 'amount_max',
     *      'receiver' => 'receiver',
     *      'is_net' => 'is_net',
     *      'is_gross' => 'is_gross',
     *      'is_grand_gross' => 'is_grand_gross',
     *      'is_hidden' => 'is_hidden',
     *  ]
     * ]
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
    public function editPackage($name, $desc, $packageDetails, $vacationDetails)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('Unauthorized');
        }
        try {
            DB::transaction(function () use ($name, $desc, $packageDetails, $vacationDetails) {
                $this->name = $name;
                $this->desc = $desc;
                $this->save();

                $this->packageDetails()->delete();
                $this->vacationDetails()->delete();

                $this->packageDetails()->createMany($packageDetails);
                $this->vacationDetails()->createMany($vacationDetails);
            });
        } catch (Exception $e) {
            report($e);
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

        if ($this->employeeInfo()->exists()) {
            throw new AppException('Cannot delete package linked to an employee');
        }
        try {
            $this->delete();
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to delete benefit package');
        }
    }
}
