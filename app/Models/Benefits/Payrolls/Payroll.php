<?php

namespace App\Models\Benefits\Payrolls;

use App\Models\Attendance\Attendance;
use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
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
        'status',
    ];

    const EMPLOYEE_SHARE_SOCIAL_INSURANCE = 0.11;
    const EMPLOYER_SHARE_SOCIAL_INSURANCE = 0.1875;
    
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

        // Use DB::transaction as described with a function that uses the variables
        DB::transaction(function() use (
            $creatorId, 
            $startDate, 
            $endDate, 
            $payrollData, 
            &$payroll, 
            &$totalPaid, 
            &$totalVacationDays, 
            &$totalVacationAmount, 
            &$totalEmployees,
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
                
                // Create payroll_employee record with fields that exist in the database schema
                $payrollEmployee = $payroll->payrollEmployees()->create([
                    'employee_id' => $employeeId,
                    'paid' => $employeeData['net_after_deductions'] ?? 0, // Use net after deductions as paid amount
                    'vacation_days' => $employeeData['vacation_days'] ?? 0,
                    'vacation_amount' => $employeeData['vacation_amount'] ?? 0,
                    'base_amount' => $employeeData['base_amount'] ?? ($employeeData['insurance_amount'] ?? 0), // Use insurance amount as base if not specified
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
                ]);

                // Create benefit payments for this employee using only base benefits data
                if (isset($employeeData['benefits'])) {
                    $employeeBenefitPaymentIds = $employee->createBenefitPaymentsForPayroll($payroll,  $employeeData['benefits']);
                    $benefitPaymentIds = array_merge($benefitPaymentIds, $employeeBenefitPaymentIds);
                }
                
                // Add employee's net payment to the total
                $totalPaid += $employeeData['net_after_deductions'] ?? 0;
                
                // Link extra payments to this payroll
                if (isset($employeeData['extra_payment_ids']) && is_array($employeeData['extra_payment_ids'])) {
                    ExtraPayment::whereIn('id', $employeeData['extra_payment_ids'])
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
}
