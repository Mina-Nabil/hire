<?php

namespace App\Models\Benefits;

use App\Exceptions\AppException;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BenefitPackage extends Model
{
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

    public function vacationBenefits()
    {
        return $this->hasMany(VacationBenefit::class);
    }

    public function employeeInfo()
    {
        return $this->hasMany(EmployeeInfo::class);
    }

    ///static functions

    /**
     * @param string $name
     * @param string $desc
     * @param array $packageDetails
     * [
     *  [
     *      'name' => 'name',
     *      'type' => 'type',
     *      'amount_min' => 'amount_min',
     *      'amount_max' => 'amount_max',
     *  ]
     * ]
     * @param array $vacationDetails
     * [
     *  [
     *      'name' => 'name',
     *      'monthly_inc_rate' => 'monthly_inc_rate',
     *      'yearly_inc_rate' => 'yearly_inc_rate',
     *      'max_days' => 'max_days',
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
     *  ]
     * ]
     * @param array $vacationDetails
     * [
     *  [
     *      'id' => 'id',
     *      'name' => 'name',
     *      'monthly_inc_rate' => 'monthly_inc_rate',
     *      'yearly_inc_rate' => 'yearly_inc_rate',
     *      'max_days' => 'max_days',
     *      'hour_price' => 'hour_price',
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
