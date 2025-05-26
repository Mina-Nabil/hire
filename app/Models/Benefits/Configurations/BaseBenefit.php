<?php

namespace App\Models\Benefits\Configurations;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Benefits\Configurations\SalaryGrade;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Users\AppLog;

class BaseBenefit extends Model
{
    const MORPH_NAME = 'base_benefit';
    protected $table = 'base_benefits';
    protected $fillable = [
        'employee_id',
        'receiver',
        'name',
        'amount',
        'type',
        'start_date',
        'end_date',
        'package_detail_id',
        'is_net',
        'is_gross',
        'is_grand_gross',
        'is_hidden',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const TYPE_MONTHLY = 'monthly';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_QUARTERLY = 'quarterly';
    const TYPE_YEARLY = 'yearly';
    const TYPE_DAILY = 'daily';

    const TYPE_LIST = [
        self::TYPE_MONTHLY,
        self::TYPE_WEEKLY,
        // self::TYPE_QUARTERLY,
        // self::TYPE_YEARLY,
        self::TYPE_DAILY,
    ];


    ///relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryGrade()
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    public function benefitPayments()
    {
        return $this->hasMany(BenefitPayment::class);
    }

    public function packageDetail()
    {
        return $this->belongsTo(PackageDetail::class);
    }

    ///model functions
    public function updateBenefit(string $name, float $amount, string $type)
    {

        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this->employee)) {
            throw new AppException('You dont have permission to update base benefit');
        }

        if ($this->benefitPayments()->exists()) {
            throw new AppException('You cannot change the benefit that has benefit payments');
        }

        if ($this->package_detail_id) {
            $package_detail = PackageDetail::find($this->package_detail_id);
            if ($package_detail->type != $type) {
                throw new AppException('You cannot change the type of a benefit that is part of a package');
            }

            if ($amount <= $package_detail->amount_min || $amount >= $package_detail->amount_max) {
                throw new AppException('You cannot change the amount of a benefit that is part of a package');
            }
        }

        try {
            $this->update([
                'name' => $name,
                'amount' => $amount,
                'type' => $type,
            ]);
            AppLog::info('Benefit Updated', "Name: $name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating benefit', $e->getMessage(), loggable: $this);
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
            AppLog::info('Benefit Deactivated', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deactivating benefit', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to deactive benefit');
        }
    }

    public function deleteBenefit()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('updateEmployeeBenefits', $this->employee)) {
            throw new AppException('You dont have permission to delete base benefit');
        }

        if ($this->benefitPayments()->exists()) {
            throw new AppException('You cannot delete the benefit that has benefit payments');
        }

        try {
            $this->delete();
            AppLog::info('Benefit Deleted', "Name: $this->name");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting benefit', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to delete benefit');
        }
    }

    ///scopes
    public function scopeBySalaryGrade($query, $salary_grade_id)
    {
        return $query
            ->join('package_details', 'base_benefits.package_detail_id', '=', 'package_details.id')
            ->where('package_details.salary_grade_id', $salary_grade_id);
    }
}
