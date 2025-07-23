<?php

namespace App\Models\Benefits\Payrolls;

use App\Exceptions\AppException;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\Overtime;
use App\Models\Payrolls\PenaltyDay;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        'total_employee_insurance',
        'total_employer_insurance',
        'total_employee_medical',
        'total_penalties_amount',
        'total_overtime_amount',
        'total_employee_base_benefits',
        'total_other_base_benefits',
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

    public function penaltyDays()
    {
        return $this->hasMany(PenaltyDay::class);
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
        $totalEmployerInsurance = 0;
        $totalEmployeeMedical = 0;
        $totalPenaltiesAmount = 0;
        $totalOvertimeAmount = 0;
        $totalEmployeeInsurance = 0;
        $totalEmployeeBaseBenefits = 0;
        $totalOtherBaseBenefits = 0;
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
            &$totalEmployerInsurance,
            &$totalEmployeeMedical,
            &$totalPenaltiesAmount,
            &$totalOvertimeAmount,
            &$totalEmployeeInsurance,
            &$totalEmployeeBaseBenefits,
            &$totalOtherBaseBenefits,
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

                $netAmountBeforeTax = ($employeeData['net_after_deductions'] ?? 0) + ($employeeData['overtime_amount'] ?? 0);
                $taxAmount = self::calculateTaxAmount($netAmountBeforeTax);
                $netAmountAfterTax = $netAmountBeforeTax - $taxAmount;

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
                    'net_after_deductions' => $netAmountBeforeTax,
                    'employee_base_benefits' => $employeeData['employee_base_benefits'] ?? 0,
                    'other_base_benefits' => $employeeData['other_base_benefits'] ?? 0,
                    'position' => $employeeData['position'] ?? 'Unknown',
                    'department' => $employeeData['department'] ?? 'Unknown',
                    'tax_amount' => $taxAmount,
                    'after_tax_salary' => $netAmountAfterTax,
                ]);

                //create penalty days
                if (isset($employeeData['penalties_days']) && is_array($employeeData['penalties_days'])) {
                    $payroll->penaltyDays()->createMany($employeeData['penalties_days']);
                }

                // Create benefit payments for this employee using only base benefits data
                if (isset($employeeData['benefits'])) {
                    $employeeBenefitPaymentIds = $employee->createBenefitPaymentsForPayroll($payroll,  $employeeData['benefits']);
                    $benefitPaymentIds = array_merge($benefitPaymentIds, $employeeBenefitPaymentIds);
                }

                // Add employee's net payment to the total
                $totalPaid += $netAmountAfterTax;
                $totalEmployeeInsurance += $employeeData['employee_insurance'] ?? 0;
                $totalEmployerInsurance += $employeeData['employer_insurance'] ?? 0;
                $totalEmployeeMedical += $employeeData['employee_medical'] ?? 0;
                $totalPenaltiesAmount += $employeeData['penalties_amount'] ?? 0;
                $totalOvertimeAmount += $employeeData['overtime_amount'] ?? 0;
                $totalTaxAmount += $taxAmount;
                $totalEmployeeBaseBenefits += $employeeData['employee_base_benefits'] ?? 0;
                $totalOtherBaseBenefits += $employeeData['other_base_benefits'] ?? 0;

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
                'total_employee_insurance' => $totalEmployeeInsurance,
                'total_employer_insurance' => $totalEmployerInsurance,
                'total_employee_medical' => $totalEmployeeMedical,
                'total_penalties_amount' => $totalPenaltiesAmount,
                'total_overtime_amount' => $totalOvertimeAmount,
                'total_employee_base_benefits' => $totalEmployeeBaseBenefits,
                'total_other_base_benefits' => $totalOtherBaseBenefits,
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
        $annualTaxableIncome = (12 * $netAfterDeductions);
        
        // If taxable income is 0 or negative, no tax
        if ($annualTaxableIncome <= 0) {
            return 0;
        }
        
        $tax = 0;
        
        // Progressive tax calculation based on the Excel formula
        // IF(C17≤60000,0,IF(C17≤75000,(C17×10%)−6000,IF(C17≤90000,(C17×15%)−9750,IF(C17≤220000,(C17×20%)−14250,IF(C17≤420000,(C17×22.5%)−19750,IF(C17≤620000,(C17×25%)−30250,IF(C17≤720000,(C17×25%)−26250,IF(C17≤820000,(C17×25%)−23500,IF(C17≤920000,(C17×25%)−20000,IF(C17≤1220000,(C17×25%)−15000,(C17×27.5%)−35500))))))))))
        
        if ($annualTaxableIncome <= 60000) {
            $tax = 0;
        } elseif ($annualTaxableIncome <= 75000) {
            $tax = ($annualTaxableIncome * 0.10) - 6000;
        } elseif ($annualTaxableIncome <= 90000) {
            $tax = ($annualTaxableIncome * 0.15) - 9750;
        } elseif ($annualTaxableIncome <= 220000) {
            $tax = ($annualTaxableIncome * 0.20) - 14250;
        } elseif ($annualTaxableIncome <= 420000) {
            $tax = ($annualTaxableIncome * 0.225) - 19750;
        } elseif ($annualTaxableIncome <= 620000) {
            $tax = ($annualTaxableIncome * 0.25) - 30250;
        } elseif ($annualTaxableIncome <= 720000) {
            $tax = ($annualTaxableIncome * 0.25) - 26250;
        } elseif ($annualTaxableIncome <= 820000) {
            $tax = ($annualTaxableIncome * 0.25) - 23500;
        } elseif ($annualTaxableIncome <= 920000) {
            $tax = ($annualTaxableIncome * 0.25) - 20000;
        } elseif ($annualTaxableIncome <= 1220000) {
            $tax = ($annualTaxableIncome * 0.25) - 15000;
        } else {
            $tax = ($annualTaxableIncome * 0.275) - 35500;
        }

        // Ensure tax is never negative
        $tax = max(0, $tax);
        
        // Return the monthly tax amount (divide annual tax by 12)
        return $tax / 12;
    }

    public function deletePayroll()
    {
        try {
            DB::transaction(function () {
                $this->payrollEmployees()->delete();
                $this->extraPayments()->update(['payroll_id' => null, 'status' => ExtraPayment::STATUS_APPROVED]);
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

    /**
     * Export payroll to Excel format with multiple sheets
     * 
     * @return BinaryFileResponse
     */
    public function exportToExcel()
    {
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // Remove default worksheet
        $spreadsheet->removeSheetByIndex(0);
        
        // 1. Create Payroll Summary Sheet
        $this->createPayrollSummarySheet($spreadsheet);
        
        // 2. Create Employee Overview Sheet
        $this->createEmployeeOverviewSheet($spreadsheet);
        
        // 3. Create individual employee sheets
        $this->createEmployeeDetailSheets($spreadsheet);
        
        // Set the first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        
        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Save the file
        $filename = storage_path('payroll_export_' . $this->id . '_' . date('Y-m-d_H-i-s') . '.xlsx');
        $writer->save($filename);
        
        return response()->download($filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Create the payroll summary sheet
     */
    private function createPayrollSummarySheet($spreadsheet)
    {
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Payroll Summary');
        
        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        
        // Title
        $summarySheet->setCellValue('A1', 'Payroll Summary Report');
        $summarySheet->mergeCells('A1:B1');
        $summarySheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        
        // Basic Information
        $summarySheet->setCellValue('A3', 'Period:');
        $summarySheet->setCellValue('B3', $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y'));
        
        $summarySheet->setCellValue('A4', 'Status:');
        $summarySheet->setCellValue('B4', ucfirst($this->status));
        
        $summarySheet->setCellValue('A5', 'Created By:');
        $summarySheet->setCellValue('B5', $this->creator->name ?? 'N/A');
        
        $summarySheet->setCellValue('A6', 'Created At:');
        $summarySheet->setCellValue('B6', $this->created_at->format('M d, Y H:i'));
        
        // Financial Summary
        $summarySheet->setCellValue('A8', 'Financial Summary');
        $summarySheet->mergeCells('A8:B8');
        $summarySheet->getStyle('A8:B8')->applyFromArray($headerStyle);
        
        $row = 10;
        $financialData = [
            'Total Employees' => $this->total_employees,
            'Total Paid' => number_format($this->total_paid, 2),
            'Employee Insurance' => number_format($this->total_employee_insurance, 2),
            'Employer Insurance' => number_format($this->total_employer_insurance, 2),
            'Employee Medical' => number_format($this->total_employee_medical, 2),
            'Penalties Amount' => number_format($this->total_penalties_amount, 2),
            'Tax Amount' => number_format($this->total_tax_amount, 2),
            'Overtime Amount' => number_format($this->total_overtime_amount, 2),
        ];
        
        foreach ($financialData as $label => $value) {
            $summarySheet->setCellValue('A' . $row, $label . ':');
            $summarySheet->setCellValue('B' . $row, $value);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'B') as $column) {
            $summarySheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
    
    /**
     * Create the employee overview sheet
     */
    private function createEmployeeOverviewSheet($spreadsheet)
    {
        $overviewSheet = $spreadsheet->createSheet();
        $overviewSheet->setTitle('Employee Overview');
        
        // Headers
        $headers = [
            'A1' => 'Employee Name',
            'B1' => 'Position',
            'C1' => 'Department', 
            'D1' => 'Gross Salary',
            'E1' => 'Social Insurance Salary',
            'F1' => 'Other Amount',
            'G1' => 'Absence Hours',
            'H1' => 'Absence Deduction',
            'I1' => 'Extra Payments',
            'J1' => 'Overtime Hours',
            'K1' => 'Overtime Amount',
            'L1' => 'Adjustment Amount',
            'M1' => 'Adjustment Description',
            'N1' => 'Before Tax',
            'O1' => 'Tax Amount',
            'P1' => 'Net After Tax'
        ];
        
        foreach ($headers as $cell => $header) {
            $overviewSheet->setCellValue($cell, $header);
        }
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        
        $overviewSheet->getStyle('A1:P1')->applyFromArray($headerStyle);
        
        // Data rows
        $row = 2;
        $payrollEmployees = $this->payrollEmployees()->with('employee')->get();
        
        foreach ($payrollEmployees as $payrollEmployee) {
            $overviewSheet->setCellValue('A' . $row, $payrollEmployee->employee->name ?? 'N/A');
            $overviewSheet->setCellValue('B' . $row, $payrollEmployee->position);
            $overviewSheet->setCellValue('C' . $row, $payrollEmployee->department);
            $overviewSheet->setCellValue('D' . $row, number_format($payrollEmployee->gross_salary, 2));
            $overviewSheet->setCellValue('E' . $row, number_format($payrollEmployee->insurance_amount, 2));
            $overviewSheet->setCellValue('F' . $row, number_format($payrollEmployee->other_amount, 2));
            $overviewSheet->setCellValue('G' . $row, number_format($payrollEmployee->total_penalty_hours ?? 0, 1) . 'h');
            $overviewSheet->setCellValue('H' . $row, number_format($payrollEmployee->direct_deduction_amount ?? 0, 2));
            $overviewSheet->setCellValue('I' . $row, number_format($payrollEmployee->extra_payments, 2));
            $overviewSheet->setCellValue('J' . $row, number_format($payrollEmployee->overtime_hours, 2) . 'h');
            $overviewSheet->setCellValue('K' . $row, number_format($payrollEmployee->overtime_amount, 2));
            $overviewSheet->setCellValue('L' . $row, number_format($payrollEmployee->adj_amount, 2));
            $overviewSheet->setCellValue('M' . $row, $payrollEmployee->adj_desc ?? '');
            $overviewSheet->setCellValue('N' . $row, number_format($payrollEmployee->net_after_deductions, 2));
            $overviewSheet->setCellValue('O' . $row, number_format($payrollEmployee->tax_amount, 2));
            $overviewSheet->setCellValue('P' . $row, number_format($payrollEmployee->after_tax_salary, 2));
            $row++;
        }
        
        // Add totals row
        $totalRow = $row;
        $overviewSheet->setCellValue('A' . $totalRow, 'TOTALS');
        $overviewSheet->setCellValue('B' . $totalRow, $this->total_employees . ' employees');
        $overviewSheet->setCellValue('D' . $totalRow, number_format($payrollEmployees->sum('gross_salary'), 2));
        $overviewSheet->setCellValue('E' . $totalRow, number_format($payrollEmployees->sum('insurance_amount'), 2));
        $overviewSheet->setCellValue('F' . $totalRow, number_format($payrollEmployees->sum('other_amount'), 2));
        $overviewSheet->setCellValue('H' . $totalRow, number_format($payrollEmployees->sum('direct_deduction_amount'), 2));
        $overviewSheet->setCellValue('I' . $totalRow, number_format($payrollEmployees->sum('extra_payments'), 2));
        $overviewSheet->setCellValue('J' . $totalRow, number_format($payrollEmployees->sum('overtime_hours'), 2) . 'h');
        $overviewSheet->setCellValue('K' . $totalRow, number_format($payrollEmployees->sum('overtime_amount'), 2));
        $overviewSheet->setCellValue('L' . $totalRow, number_format($payrollEmployees->sum('adj_amount'), 2));
        $overviewSheet->setCellValue('N' . $totalRow, number_format($payrollEmployees->sum('net_after_deductions'), 2));
        $overviewSheet->setCellValue('O' . $totalRow, number_format($payrollEmployees->sum('tax_amount'), 2));
        $overviewSheet->setCellValue('P' . $totalRow, number_format($payrollEmployees->sum('after_tax_salary'), 2));
        
        // Style totals row
        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0']
            ]
        ];
        $overviewSheet->getStyle('A' . $totalRow . ':P' . $totalRow)->applyFromArray($totalStyle);
        
        // Auto-size columns
        foreach (range('A', 'P') as $column) {
            $overviewSheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
    
    /**
     * Create individual employee detail sheets
     */
    private function createEmployeeDetailSheets($spreadsheet)
    {
        $payrollEmployees = $this->payrollEmployees()->with('employee')->get();
        
        foreach ($payrollEmployees as $payrollEmployee) {
            $employee = $payrollEmployee->employee;
            if (!$employee) continue;
            
            // Create sheet for employee
            $employeeSheet = $spreadsheet->createSheet();
            $safeSheetName = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $employee->name), 0, 31);
            $employeeSheet->setTitle($safeSheetName);
            
            // Employee info header
            $employeeSheet->setCellValue('A1', 'Employee Details: ' . $employee->name);
            $employeeSheet->mergeCells('A1:F1');
            
            $headerStyle = [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            $employeeSheet->getStyle('A1:F1')->applyFromArray($headerStyle);
            
            // Employee summary
            $employeeSheet->setCellValue('A3', 'Position:');
            $employeeSheet->setCellValue('B3', $payrollEmployee->position);
            $employeeSheet->setCellValue('A4', 'Department:');
            $employeeSheet->setCellValue('B4', $payrollEmployee->department);
            $employeeSheet->setCellValue('A5', 'Gross Salary:');
            $employeeSheet->setCellValue('B5', number_format($payrollEmployee->gross_salary, 2));
            $employeeSheet->setCellValue('A6', 'Net After Tax:');
            $employeeSheet->setCellValue('B6', number_format($payrollEmployee->after_tax_salary, 2));
            
            // Attendance Records
            $employeeSheet->setCellValue('A8', 'Attendance Records');
            $employeeSheet->mergeCells('A8:F8');
            $employeeSheet->getStyle('A8:F8')->applyFromArray($headerStyle);
            
            // Attendance headers
            $attendanceHeaders = [
                'A10' => 'Day',
                'B10' => 'Date', 
                'C10' => 'Check In',
                'D10' => 'Check Out',
                'E10' => 'Total Hours',
                'F10' => 'Status'
            ];
            
            foreach ($attendanceHeaders as $cell => $header) {
                $employeeSheet->setCellValue($cell, $header);
            }
            $employeeSheet->getStyle('A10:F10')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ]
            ]);
            
            // Get attendance records for this payroll period
            $attendanceRecords = $employee->attendances()
                ->whereBetween('date', [$this->start_date, $this->end_date])
                ->orderBy('date')
                ->get();
            
            $row = 11;
            foreach ($attendanceRecords as $attendance) {
                $date = \Carbon\Carbon::parse($attendance->date);
                $employeeSheet->setCellValue('A' . $row, $date->format('l'));
                $employeeSheet->setCellValue('B' . $row, $date->format('d/m/Y'));
                $employeeSheet->setCellValue('C' . $row, $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i A') : 'N/A');
                $employeeSheet->setCellValue('D' . $row, $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i A') : 'N/A');
                
                $hoursText = $attendance->hours ? $attendance->hours . 'h' : 'N/A';
                if ($attendance->penalized_hours > 0) {
                    $penaltyText = $attendance->penalized_hours > 1.0 
                        ? number_format($attendance->penalized_hours, 0) . 'h' 
                        : number_format($attendance->penalized_hours * 60, 2) . 'min';
                    $hoursText .= ' -(' . $penaltyText . ')';
                }
                $employeeSheet->setCellValue('E' . $row, $hoursText);
                
                $status = 'Present';
                if ($attendance->status === 'absent') $status = 'Absent';
                elseif ($attendance->status === 'late') $status = 'Late';
                elseif (!$attendance->is_approved) $status = 'Pending';
                
                $employeeSheet->setCellValue('F' . $row, $status);
                $row++;
            }
            
            // Missing Days section
            $missingDaysStartRow = $row + 2;
            $employeeSheet->setCellValue('A' . $missingDaysStartRow, 'Missing Days');
            $employeeSheet->mergeCells('A' . $missingDaysStartRow . ':B' . $missingDaysStartRow);
            $employeeSheet->getStyle('A' . $missingDaysStartRow . ':B' . $missingDaysStartRow)->applyFromArray($headerStyle);
            
            // Missing days headers
            $missingDaysHeaderRow = $missingDaysStartRow + 2;
            $employeeSheet->setCellValue('A' . $missingDaysHeaderRow, 'Missed Day');
            $employeeSheet->setCellValue('B' . $missingDaysHeaderRow, 'Hours');
            $employeeSheet->getStyle('A' . $missingDaysHeaderRow . ':B' . $missingDaysHeaderRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ]
            ]);
            
            // Get missing days for this payroll period
            $missingDays = $employee->missingDays()
                ->whereBetween('date', [$this->start_date, $this->end_date])
                ->orderBy('date')
                ->get();
            
            $missingRow = $missingDaysHeaderRow + 1;
            foreach ($missingDays as $missingDay) {
                $employeeSheet->setCellValue('A' . $missingRow, \Carbon\Carbon::parse($missingDay->date)->format('d M Y'));
                $employeeSheet->setCellValue('B' . $missingRow, $missingDay->hours);
                $missingRow++;
            }
            
            // Auto-size columns
            foreach (range('A', 'F') as $column) {
                $employeeSheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }
}
