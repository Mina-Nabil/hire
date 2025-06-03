<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payroll extends Model
{
    const MORPH_NAME = 'payroll';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
    ];

    protected $table = 'payrolls';
    protected $fillable = [
        'creator_id',
        'start_date',
        'end_date',
        'total_paid',
        'total_vacation_days',
        'total_vacation_amount',
        'total_employees',
        'total_tax_amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    const EMPLOYEE_SHARE_SOCIAL_INSURANCE = 0.11;
    const EMPLOYER_SHARE_SOCIAL_INSURANCE = 0.1875;

    // Tax brackets and rates
    const TAX_BRACKET_1 = 30000;   // 0% up to 30,000
    const TAX_BRACKET_2 = 45000;   // 10% from 30,001 to 45,000
    const TAX_BRACKET_3 = 60000;   // 15% from 45,001 to 60,000
    const TAX_BRACKET_4 = 200000;  // 20% from 60,001 to 200,000
    const TAX_BRACKET_5 = 400000;  // 22.5% from 200,001 to 400,000
    const TAX_BRACKET_6 = 600000;  // 25% from 400,001 to 600,000
    const TAX_BRACKET_7 = 700000;  // 25% from 600,001 to 700,000
    const TAX_BRACKET_8 = 800000;  // 25% from 700,001 to 800,000
    const TAX_BRACKET_9 = 900000;  // 25% from 800,001 to 900,000
    const TAX_BRACKET_10 = 1200000; // 25% from 900,001 to 1,200,000
    // 27.5% above 1,200,000
    
    const TAX_YEARLY_ALLOWANCE = 15000;

    /**
     * Get the employee records for this payroll
     */
    public function payrollEmployees()
    {
        return $this->hasMany(PayrollEmployee::class);
    }

    /**
     * Get the employees for this payroll
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'payroll_employees');
    }

    /**
     * Get the attendance records for this payroll
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the extra payments for this payroll
     */
    public function extraPayments()
    {
        return $this->hasMany(ExtraPayment::class);
    }

    /**
     * Get the creator of this payroll
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the benefit payments for this payroll
     */
    public function benefitPayments()
    {
        return $this->hasMany(BenefitPayment::class);
    }

    public function overtimes()
    {
        return $this->hasMany(\App\Models\Attendance\Overtime::class);
    }

    /**
     * Approve the payroll and update related records
     * 
     * @param int|null $approverId ID of the user approving the payroll
     * @return bool
     */
    public function approve()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You dont have permission to update payroll');
        }

        if ($this->status === self::STATUS_APPROVED) {
            return true; // Already approved
        }

        try {
            return DB::transaction(function () use ($loggedInUser) {
                // Update payroll status
                $this->update([
                    'status' => self::STATUS_APPROVED,
                ]);

                // Update all related benefit payments to paid status
                $this->benefitPayments()->update([
                    'status' => BenefitPayment::STATUS_PAID,
                ]);

                // Update all related extra payments to paid status
                $this->extraPayments()->update([
                    'status' => ExtraPayment::STATUS_PAID,
                ]);

                // Approve all pending overtime records that were auto-created during payroll generation
                $pendingOvertimes = $this->overtimes()
                    ->where('status', Overtime::STATUS_PENDING)
                    ->where('admin_note', 'LIKE', '%Auto-created from attendance during payroll creation%')
                    ->get();

                foreach ($pendingOvertimes as $overtime) {
                    $overtime->update([
                        'status' => Overtime::STATUS_APPROVED,
                        'approved_at' => now(),
                        'admin_note' => $overtime->admin_note . ' - Auto-approved with payroll approval',
                    ]);

                    AppLog::info(
                        'Overtime Auto-Approved',
                        "Auto-approved overtime for {$overtime->employee->name} on {$overtime->date} during payroll approval",
                        loggable: $overtime
                    );
                }

                // Log the approval
                AppLog::info(
                    'Payroll Approved',
                    'Approved payroll for period ' . $this->start_date . ' to ' . $this->end_date .
                        ' with ' . $this->total_employees . ' employees. Total amount: ' . $this->total_paid,
                    loggable: $this
                );

                return true;
            });
        } catch (\Exception $e) {
            AppLog::error('Error approving payroll', $e->getMessage());
            throw new AppException('Error approving payroll');
        }
    }

    /**
     * Create a new payroll with all related records
     * 
     * @param int $creatorId ID of the user creating the payroll
     * @param string $startDate Start date of the payroll period (Y-m-d)
     * @param string $endDate End date of the payroll period (Y-m-d)
     * @param array $payrollData Pre-calculated payroll data for each employee
     * @param array $departmentIds Optional department IDs that were used to filter employees
     * @return Payroll The created payroll
     */
    public static function createPayroll($creatorId, $startDate, $endDate, $payrollData = [])
    {
        $payroll = null;
        $totalPaid = 0;
        $totalVacationDays = 0;
        $totalVacationAmount = 0;
        $totalEmployees = count($payrollData);
        $benefitPaymentIds = [];
        $totalTaxAmount = 0;

        // Use DB::transaction as described with a function that uses the variables
        DB::transaction(function () use (
            $creatorId,
            $startDate,
            $endDate,
            $payrollData,
            &$payroll,
            &$totalPaid,
            &$totalVacationDays,
            &$totalVacationAmount,
            &$totalEmployees,
            &$totalTaxAmount,
            &$benefitPaymentIds
        ) {
            // 1. Create the payroll record
            $payroll = self::create([
                'creator_id' => $creatorId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_paid' => 0, // Will update at the end
                'total_vacation_days' => 0, // Will update at the end
                'total_vacation_amount' => 0, // Will update at the end
                'total_employees' => 0, // Will update at the end
                'status' => self::STATUS_PENDING,
            ]);

            // 2. Process each employee data from the provided payroll data
            foreach ($payrollData as $employeeData) {
                $employeeId = $employeeData['employee_id'];
                $employee = Employee::find($employeeId);

                if (!$employee) {
                    continue; // Skip if employee not found
                }

                $taxAmount = self::calculateTaxAmount($employeeData['net_after_deductions']);


                // Create payroll_employee record with fields that exist in the database schema
                $payrollEmployee = $payroll->payrollEmployees()->create([
                    'employee_id' => $employeeId,
                    'paid' => $employeeData['net_after_deductions'] ?? 0, // Use net after deductions as paid amount
                    'vacation_days' => $employeeData['vacation_days'] ?? 0,
                    'vacation_amount' => $employeeData['vacation_amount'] ?? 0,
                    'base_amount' => $employeeData['base_amount'] ?? ($employeeData['insurance_amount'] ?? 0), // Use Social Insurance Salary as base if not specified
                    'gross_salary' => $employeeData['gross_salary'] ?? 0,
                    'insurance_amount' => $employeeData['insurance_amount'] ?? 0,
                    'other_amount' => $employeeData['other_amount'] ?? 0,
                    'employee_insurance' => $employeeData['employee_insurance'] ?? 0,
                    'employer_insurance' => $employeeData['employer_insurance'] ?? 0,
                    'total_insurance' => $employeeData['total_insurance'] ?? 0,
                    'employee_medical' => $employeeData['employee_medical'] ?? 0,
                    'total_medical' => $employeeData['total_medical'] ?? 0,
                    'employee_deductions' => $employeeData['employee_deductions'] ?? 0,
                    'penalties_days' => $employeeData['penalties_days'] ?? 0,
                    'penalties_amount' => $employeeData['penalties_amount'] ?? 0,
                    'total_penalty_hours' => $employeeData['total_penalty_hours'] ?? 0,
                    'vacation_offset_hours' => $employeeData['vacation_offset_hours'] ?? 0,
                    'new_vacation_hours' => $employeeData['new_vacation_hours'] ?? 0,
                    'direct_deduction_hours' => $employeeData['direct_deduction_hours'] ?? 0,
                    'direct_deduction_amount' => $employeeData['direct_deduction_amount'] ?? 0,
                    'overtime_hours' => $employeeData['overtime_hours'] ?? 0,
                    'overtime_amount' => $employeeData['overtime_amount'] ?? 0,
                    'net_after_penalty' => $employeeData['net_after_penalty'] ?? 0,
                    'extra_payments' => $employeeData['extra_payments'] ?? 0,
                    'adj_amount' => $employeeData['adj_amount'] ?? 0,
                    'adj_desc' => $employeeData['adj_desc'] ?? '',
                    'net_after_deductions' => $employeeData['net_after_deductions'] ?? 0,
                    'employee_base_benefits' => $employeeData['employee_base_benefits'] ?? 0,
                    'other_base_benefits' => $employeeData['other_base_benefits'] ?? 0,
                    'position' => $employeeData['position'] ?? 'Unknown',
                    'department' => $employeeData['department'] ?? 'Unknown',
                    'tax_amount' => $taxAmount,
                ]);

                // Create benefit payments for this employee using only base benefits data
                if (isset($employeeData['benefits'])) {
                    $employeeBenefitPaymentIds = $employee->createBenefitPaymentsForPayroll($payroll,  $employeeData['benefits']);
                    $benefitPaymentIds = array_merge($benefitPaymentIds, $employeeBenefitPaymentIds);
                }

                // Add employee's net payment to the total
                $totalPaid += $employeeData['net_after_deductions'] ?? 0;

                $totalTaxAmount += $taxAmount;

                // Link extra payments to this payroll
                if (isset($employeeData['extra_payment_ids']) && is_array($employeeData['extra_payment_ids'])) {
                    ExtraPayment::whereIn('id', $employeeData['extra_payment_ids'])->where('amount', '<', 0)
                        ->update(['payroll_id' => $payroll->id]);
                } else {
                    // Fallback to previous method if IDs not provided
                    $employee->extraPayments()
                        ->where('status', ExtraPayment::STATUS_APPROVED)
                        ->whereBetween('due_date', [$startDate, $endDate])
                        ->whereNull('payroll_id')
                        ->update(['payroll_id' => $payroll->id]);
                }

                // Link attendance records to this payroll if requested
                if (isset($employeeData['attendance_ids']) && is_array($employeeData['attendance_ids'])) {
                    Attendance::whereIn('id', $employeeData['attendance_ids'])
                        ->update(['payroll_id' => $payroll->id]);
                } else {
                    // Fallback to previous method if IDs not provided
                    $employee->attendances()
                        ->whereBetween('date', [$startDate, $endDate])
                        ->whereNull('payroll_id')
                        ->update(['payroll_id' => $payroll->id]);
                }

                // Link overtime records to this payroll
                if (isset($employeeData['overtime_ids']) && is_array($employeeData['overtime_ids'])) {
                    Overtime::whereIn('id', $employeeData['overtime_ids'])
                        ->update(['payroll_id' => $payroll->id]);
                } else {
                    // Fallback to previous method if IDs not provided
                    $employee->overtimes()
                        ->where('status', Overtime::STATUS_APPROVED)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->whereNull('payroll_id')
                        ->update(['payroll_id' => $payroll->id]);
                }
            }

            // 3. Update the payroll with the final totals
            $payroll->update([
                'total_paid' => $totalPaid,
                'total_vacation_days' => $totalVacationDays,
                'total_vacation_amount' => $totalVacationAmount,
                'total_employees' => $totalEmployees,
                'total_tax_amount' => $totalTaxAmount,
            ]);

            // Log the creation of the payroll
            AppLog::info(
                'Payroll Created',
                'Created payroll for period ' . $startDate . ' to ' . $endDate . ' with ' . $totalEmployees . ' employees',
                loggable: $payroll
            );
        });

        // Return the created payroll instance
        return $payroll;
    }

    public static function calculateTaxAmount($netAfterDeductions) : float
    {
        // Calculate annual taxable income: (12 * monthly_net_salary) - yearly_allowance
        $annualTaxableIncome = (12 * $netAfterDeductions) - self::TAX_YEARLY_ALLOWANCE;
        
        // If taxable income is 0 or negative, no tax
        if ($annualTaxableIncome <= 0) {
            return 0;
        }
        
        $tax = 0;
        
        // Progressive tax calculation based on the Excel formula
        if ($annualTaxableIncome <= self::TAX_BRACKET_1) {
            // 0% tax up to 30,000
            $tax = 0;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_2) {
            // 10% on amount above 30,000 up to 45,000
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_1) * 0.10;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_3) {
            // 15% on amount above 45,000 up to 60,000, plus previous bracket tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_2) * 0.15 + 1500;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_4) {
            // 20% on amount above 60,000 up to 200,000, plus previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_3) * 0.20 + 1500 + 2250;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_5) {
            // 22.5% on amount above 200,000 up to 400,000, plus previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_4) * 0.225 + 1500 + 2250 + 28000;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_6) {
            // 25% on amount above 400,000 up to 600,000, plus previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_5) * 0.25 + 1500 + 2250 + 28000 + 45000;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_7) {
            // 25% on amount above 400,000 up to 700,000, plus adjusted previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_5) * 0.25 + 4500 + 2250 + 28000 + 45000;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_8) {
            // 25% on amount above 400,000 up to 800,000, plus adjusted previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_5) * 0.25 + 9000 + 28000 + 45000;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_9) {
            // 25% on amount above 400,000 up to 900,000, plus adjusted previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_5) * 0.25 + 40000 + 45000;
        } elseif ($annualTaxableIncome <= self::TAX_BRACKET_10) {
            // 25% on amount above 400,000 up to 1,200,000, plus adjusted previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_5) * 0.25 + 90000;
        } else {
            // 27.5% on amount above 1,200,000, plus previous brackets tax
            $tax = ($annualTaxableIncome - self::TAX_BRACKET_10) * 0.275 + 300000;
        }

        
        // Return the monthly tax amount (divide annual tax by 12)
        return $tax / 12;
    }

    public function deletePayroll()
    {
        try {
            DB::transaction(function () {
                $this->payrollEmployees()->delete();
                $this->delete();
            });
        } catch (\Exception $e) {
            AppLog::error('Error deleting payroll', $e->getMessage());
            throw new AppException('Error deleting payroll');
        }
    }

    ///attributes
    public function getTitleAttribute()
    {
        return 'Payroll ' . $this->start_date->format('Y-m-d') . ' -> ' . $this->end_date->format('Y-m-d');
    }
}
