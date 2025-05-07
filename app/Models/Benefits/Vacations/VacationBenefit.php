<?php

namespace App\Models\Benefits\Vacations;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Benefits\Vacations\VacationPayment;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Configurations\PackageDetail;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VacationBenefit extends Model
{
    const MORPH_NAME = 'vacation_benefit';
    protected $table = 'vacation_benefits';
    protected $fillable = [
        'name',
        'inc_rate',
        'type',
        'hour_price',
        'max_balance',
        'current_balance',
        'start_date',
        'end_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacationDetail()
    {
        return $this->belongsTo(VacationDetail::class);
    }

    public function vacationPayments()
    {
        return $this->hasMany(VacationPayment::class);
    }

    public function appliedVacations()
    {
        return $this->hasMany(AppliedVacation::class);
    }

    public function gainedVacations()
    {
        return $this->hasMany(GainedVacation::class);
    }

    ///model functions
    public function updateBenefit(string $name, string $type, float $inc_rate, float $hour_price, float $max_balance)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this->employee)) {
            throw new AppException('You dont have permission to update vacation benefit');
        }

        if ($this->package_detail_id != null && ($name || $type)) {
            throw new AppException('You cannot change the name or type of a benefit that is part of a package');
        }

        if ($this->package_detail_id) {
            $package_detail = PackageDetail::find($this->package_detail_id);
            if ($package_detail->type != $type) {
                throw new AppException('You cannot change the type of a benefit that is part of a package');
            }

            if ($inc_rate <= $package_detail->inc_rate_min || $inc_rate >= $package_detail->inc_rate_max) {
                throw new AppException('Increase rate is not in the range of the package detail');
            }

            if ($hour_price <= $package_detail->hour_price_min || $hour_price >= $package_detail->hour_price_max) {
                throw new AppException('Hour price is not in the range of the package detail');
            }

            if ($max_balance <= $package_detail->max_balance_min || $max_balance >= $package_detail->max_balance_max) {
                throw new AppException('Maximum balance is not in the range of the package detail');
            }
        }

        if ($this->gainedVacations()->exists()) {
            throw new AppException('You cannot change the benefit that has gained vacations');
        }

        if ($this->appliedVacations()->exists()) {
            throw new AppException('You cannot change the benefit that has applied vacations');
        }

        if ($this->vacationPayments()->exists()) {
            throw new AppException('You cannot change the benefit that has vacation payments');
        }

        if ($this->vacationDetail()->exists()) {
            throw new AppException('You cannot change the benefit that has a vacation detail');
        }
        try {
            $this->update([
                'name' => $name,
                'type' => $type,
                'inc_rate' => $inc_rate,
                'hour_price' => $hour_price,
                'max_balance' => $max_balance
            ]);
        } catch (Exception $e) {
            throw new AppException('Failed to update benefit');
        }
    }

    public function deactiveBenefit()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this->employee)) {
            throw new AppException('You dont have permission to deactive vacation benefit');
        }

        if ($this->end_date) {
            throw new AppException('Benefit is already deactivated');
        }
        try {
            $this->update([
                'end_date' => now()
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to deactive benefit');
        }
    }

    public function deleteBenefit()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this->employee)) {
            throw new AppException('You dont have permission to delete vacation benefit');
        }

        if ($this->gainedVacations()->exists()) {
            throw new AppException('You cannot delete the benefit that has gained vacations');
        }

        if ($this->appliedVacations()->exists()) {
            throw new AppException('You cannot delete the benefit that has applied vacations');
        }

        if ($this->vacationPayments()->exists()) {
            throw new AppException('You cannot delete the benefit that has vacation payments');
        }

        if ($this->vacationDetail()->exists()) {
            throw new AppException('You cannot delete the benefit that has a vacation detail');
        }

        try {
            $this->delete();
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to delete benefit');
        }
    }
}
