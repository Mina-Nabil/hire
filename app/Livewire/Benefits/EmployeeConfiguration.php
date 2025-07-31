<?php

namespace App\Livewire\Benefits;

use App\Models\Personel\Employee;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Extras\Loan;
use App\Models\Benefits\Extras\Purchase;
use App\Models\Benefits\Payrolls\ExtraPayment;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Carbon\Carbon;
use Exception;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;
use App\Models\Benefits\Payrolls\PayrollEmployee;

class EmployeeConfiguration extends Component
{
    use AlertFrontEnd, WithPagination;

    public Employee $employee;
    public $activeTab = 'info';

    // Base Info Edit Modal
    public $editBaseInfoModal = false;
    public $name;
    public $name_ar;
    public $mother_name;
    public $email;
    public $phone;
    public $address;
    public $nationality;
    public $gender;
    public $birth_date;
    public $employment_date;
    public $termination_date;
    public $release_date;
    public $absent_date;
    public $release_note;

    public $benefitIncrementTypes = BaseBenefit::TYPE_LIST;
    public $vacationBenefitTypes = VacationDetail::TYPE_LIST;

    public $benefitReceivers = PackageDetail::RECEIVER_LIST;

    // Custom Base Benefit Modal
    public $showAddCustomBaseBenefitModal = false;
    public $baseBenefit = [
        'name' => '',
        'amount' => 0,
        'type' => '',
        'receiver' => 'employee',
        'start_date' => '',
    ];

    // Custom Vacation Benefit Modal
    public $showAddCustomVacationBenefitModal = false;
    public $vacationBenefit = [
        'name' => '',
        'inc_rate' => 0,
        'hour_price' => 0,
        'current_balance' => 0,
        'max_balance' => 0,
        'type' => '',
        'start_date' => '',
        'apply_deadline' => null,
    ];

    // Loan Modal
    public $showAddLoanModal = false;
    public $loan = [
        'amount' => 0,
        'desc' => '',
    ];
    public $loanPayments = [];
    public $loanRemainingAmount = 0;

    // Purchase Modal
    public $showAddPurchaseModal = false;
    public $purchase = [
        'amount' => 0,
        'desc' => '',
    ];
    public $purchasePayments = [];
    public $purchaseRemainingAmount = 0;

    // Add Extra Payment Modal
    public $showAddExtraPaymentModal = false;
    public $extraPayment = [
        'name' => '',
        'amount' => 0,
        'due_date' => '',
        'desc' => '',
    ];

    public $listeners = ['refreshConfiguration'];

    public function refreshConfiguration()
    {
        $this->mount($this->employee);
    }

    public function editConfiguration()
    {
        $this->dispatch('editConfiguration', employeeId: $this->employee->id);
    }

    public function editAttendance()
    {
        $this->dispatch('editAttendance', employeeId: $this->employee->id);
    }

    public function editVacations()
    {
        $this->dispatch('editVacations', employeeId: $this->employee->id);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        // Reload data when switching tabs to ensure data is fresh
    }

    public function getBenefitTypeLabel($type)
    {
        $types = [
            BaseBenefit::TYPE_MONTHLY => 'Monthly',
            BaseBenefit::TYPE_WEEKLY => 'Weekly',
            BaseBenefit::TYPE_QUARTERLY => 'Quarterly',
            BaseBenefit::TYPE_YEARLY => 'Yearly',
            BaseBenefit::TYPE_DAILY => 'Daily'
        ];

        return $types[$type] ?? $type;
    }

    public function getReceiverLabel($receiver)
    {
        $receivers = [
            'employee' => 'Employee',
            'taxes' => 'Taxes',
            'insurance' => 'Insurance',
            'medical' => 'Medical',
            'other' => 'Other'
        ];

        return $receivers[$receiver] ?? $receiver;
    }

    public function getAttendanceCalculationLabel($calculation)
    {
        $calculations = [
            'flexible' => 'Flexible',
            'semi-flexible' => 'Semi-Flexible',
            'fixed' => 'Fixed'
        ];

        return $calculations[$calculation] ?? $calculation;
    }

    public function mount(Employee $employee)
    {
        $this->employee = $employee;

        // Initialize date fields with current date
        $this->baseBenefit['start_date'] = now()->format('Y-m-d');
        $this->vacationBenefit['start_date'] = now()->format('Y-m-d');
    }

    public function render()
    {
        // Load base benefits
        $employeeBenefits = BaseBenefit::where('employee_id', $this->employee->id)
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // Load vacation benefits - using VacationBenefit instead of VacationDetail
        $employeeVacations = VacationBenefit::where('employee_id', $this->employee->id)
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // Load payments
        $employeePayments = BenefitPayment::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $extraPayments = ExtraPayment::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $payrollRecords = PayrollEmployee::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load loans
        $loans = Loan::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load purchases
        $purchases = Purchase::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load applied vacations
        $appliedVacations = AppliedVacation::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load gained vacations
        $gainedVacations = GainedVacation::where('employee_id', $this->employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('livewire.benefits.employee-configuration', [
            'employeeBenefits' => $employeeBenefits,
            'employeeVacations' => $employeeVacations,
            'employeePayments' => $employeePayments,
            'extraPayments' => $extraPayments,
            'payrollRecords' => $payrollRecords,
            'loans' => $loans,
            'purchases' => $purchases,
            'appliedVacations' => $appliedVacations,
            'gainedVacations' => $gainedVacations,
        ]);
    }

    // --------- Custom Base Benefit Functions ---------
    public function addCustomBaseBenefit()
    {
        $this->resetBaseBenefitForm();
        $this->showAddCustomBaseBenefitModal = true;
    }

    public function closeAddCustomBaseBenefitModal()
    {
        $this->showAddCustomBaseBenefitModal = false;
    }

    public function resetBaseBenefitForm()
    {
        $this->baseBenefit = [
            'name' => '',
            'amount' => 0,
            'type' => '',
            'receiver' => 'employee',
            'start_date' => now()->format('Y-m-d'),
        ];
    }

    public function saveCustomBaseBenefit()
    {
        $this->validate([
            'baseBenefit.name' => 'required|string',
            'baseBenefit.amount' => 'required|numeric|min:0',
            'baseBenefit.type' => 'required|string',
            'baseBenefit.receiver' => 'required|string',
            'baseBenefit.start_date' => 'required|date',
        ], [
            'baseBenefit.name.required' => 'The name is required.',
            'baseBenefit.amount.required' => 'The amount is required.',
            'baseBenefit.type.required' => 'The type is required.',
            'baseBenefit.receiver.required' => 'The receiver is required.',
            'baseBenefit.start_date.required' => 'The start date is required.',
        ]);

        try {
            $this->employee->addCustomBaseBenefit(
                $this->baseBenefit['name'],
                $this->baseBenefit['amount'],
                $this->baseBenefit['type'],
                $this->baseBenefit['receiver'],
                Carbon::parse($this->baseBenefit['start_date'])
            );

            $this->closeAddCustomBaseBenefitModal();
            $this->alertSuccess('Custom base benefit added successfully.');
        } catch (Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    // --------- Custom Vacation Benefit Functions ---------
    public function addCustomVacationBenefit()
    {
        $this->resetVacationBenefitForm();
        $this->showAddCustomVacationBenefitModal = true;
    }

    public function closeAddCustomVacationBenefitModal()
    {
        $this->showAddCustomVacationBenefitModal = false;
    }

    public function resetVacationBenefitForm()
    {
        $this->vacationBenefit = [
            'name' => '',
            'inc_rate' => 0,
            'hour_price' => 0,
            'current_balance' => 0,
            'max_balance' => 0,
            'type' => '',
            'start_date' => now()->format('Y-m-d'),
            'apply_deadline' => 0,
        ];
    }

    public function saveCustomVacationBenefit()
    {
        $this->validate([
            'vacationBenefit.name' => 'required|string',
            'vacationBenefit.inc_rate' => 'required|numeric|min:0',
            'vacationBenefit.hour_price' => 'required|numeric|min:0',
            'vacationBenefit.current_balance' => 'required|numeric|min:0',
            'vacationBenefit.max_balance' => 'required|numeric|min:0',
            'vacationBenefit.type' => 'required|string',
            'vacationBenefit.start_date' => 'required|date',
            'vacationBenefit.apply_deadline' => 'nullable|integer|min:0',
        ], [
            'vacationBenefit.name.required' => 'The name is required.',
            'vacationBenefit.inc_rate.required' => 'The inc rate is required.',
            'vacationBenefit.hour_price.required' => 'The hour price is required.',
            'vacationBenefit.current_balance.required' => 'The current balance is required.',
            'vacationBenefit.max_balance.required' => 'The max balance is required.',
        ]);

        try {
            $this->employee->addCustomVacationBenefit(
                $this->vacationBenefit['name'],
                $this->vacationBenefit['inc_rate'],
                $this->vacationBenefit['hour_price'],
                $this->vacationBenefit['current_balance'],
                $this->vacationBenefit['max_balance'],
                $this->vacationBenefit['type'],
                Carbon::parse($this->vacationBenefit['start_date']),
                $this->vacationBenefit['apply_deadline']
            );

            $this->closeAddCustomVacationBenefitModal();
            $this->alertSuccess('Custom vacation benefit added successfully.');
        } catch (Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    // --------- Loan Functions ---------
    public function addLoan()
    {
        $this->resetLoanForm();
        $this->showAddLoanModal = true;
    }

    public function closeAddLoanModal()
    {
        $this->showAddLoanModal = false;
    }

    public function resetLoanForm()
    {
        $this->loan = [
            'amount' => 0,
            'desc' => '',
        ];
        $this->loanPayments = [];
        $this->loanRemainingAmount = 0;
    }

    public function addLoanPayment()
    {
        $this->loanPayments[] = [
            'amount' => 0,
            'due_date' => now()->format('Y-m-d'),
            'desc' => '',
        ];
        $this->updateRemainingAmount();
    }

    public function removeLoanPayment($index)
    {
        array_splice($this->loanPayments, $index, 1);
        $this->updateRemainingAmount();
    }

    public function updateRemainingAmount()
    {
        $totalPayments = 0;
        foreach ($this->loanPayments as $payment) {
            $totalPayments += floatval($payment['amount']);
        }
        $this->loanRemainingAmount = floatval($this->loan['amount']) - $totalPayments;
    }

    public function saveLoan()
    {
        $this->validate([
            'loan.amount' => 'required|numeric|min:0.01',
            'loanPayments.*.amount' => 'required|numeric|min:0.01',
            'loanPayments.*.due_date' => 'required|date',
        ], [
            'loan.amount.required' => 'The amount is required.',
            'loanPayments.*.amount.required' => 'The amount is required.',
            'loanPayments.*.due_date.required' => 'The due date is required.',
        ]);

        if ($this->loanRemainingAmount != 0) {
            $this->alertError('Total payment amounts must equal the loan amount.');
            return;
        }

        try {
            $loan = new Loan();
            $loan->createLoan(
                $this->employee,
                $this->loan['amount'],
                $this->loan['desc'],
                $this->loanPayments
            );

            $this->closeAddLoanModal();
            $this->alertSuccess('Loan added successfully.');
        } catch (\Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    // --------- Purchase Functions ---------
    public function addPurchase()
    {
        $this->resetPurchaseForm();
        $this->showAddPurchaseModal = true;
    }

    public function closeAddPurchaseModal()
    {
        $this->showAddPurchaseModal = false;
    }

    public function resetPurchaseForm()
    {
        $this->purchase = [
            'amount' => 0,
            'desc' => '',
        ];
        $this->purchasePayments = [];
        $this->purchaseRemainingAmount = 0;
    }

    public function addPurchasePayment()
    {
        $this->purchasePayments[] = [
            'amount' => 0,
            'due_date' => now()->format('Y-m-d'),
            'desc' => '',
        ];
        $this->updateRemainingPurchaseAmount();
    }

    public function removePurchasePayment($index)
    {
        array_splice($this->purchasePayments, $index, 1);
        $this->updateRemainingPurchaseAmount();
    }

    public function updateRemainingPurchaseAmount()
    {
        $totalPayments = 0;
        foreach ($this->purchasePayments as $payment) {
            $totalPayments += floatval($payment['amount']);
        }
        $this->purchaseRemainingAmount = floatval($this->purchase['amount']) - $totalPayments;
    }

    public function savePurchase()
    {
        $this->validate([
            'purchase.amount' => 'required|numeric|min:0.01',
            'purchasePayments.*.amount' => 'required|numeric|min:0.01',
            'purchasePayments.*.due_date' => 'required|date',
        ], [
            'purchase.amount.required' => 'The amount is required.',
            'purchasePayments.*.amount.required' => 'The amount is required.',
            'purchasePayments.*.due_date.required' => 'The due date is required.',
        ]);

        if ($this->purchaseRemainingAmount != 0) {
            $this->alertError('Total payment amounts must equal the purchase amount.');
            return;
        }

        try {
            $purchase = new Purchase();
            $purchase->createPurchase(
                $this->employee,
                $this->purchase['amount'],
                $this->purchase['desc'],
                $this->purchasePayments
            );

            $this->closeAddPurchaseModal();
            $this->alertSuccess('Purchase added successfully.');
        } catch (Exception $e) {
            $this->alertError('Error: ' . $e->getMessage());
        }
    }

    public function addExtraPayment()
    {
        $this->resetExtraPaymentForm();
        $this->showAddExtraPaymentModal = true;
    }

    public function closeAddExtraPaymentModal()
    {
        $this->showAddExtraPaymentModal = false;
    }

    public function resetExtraPaymentForm()
    {
        $this->extraPayment = [
            'name' => '',
            'amount' => 0,
            'due_date' => now()->format('Y-m-d'),
            'desc' => '',
        ];
    }

    public function saveExtraPayment()
    {
        $this->validate([
            'extraPayment.amount' => 'required|numeric|min:0.01',
            'extraPayment.due_date' => 'required|date',
            'extraPayment.desc' => 'required|string',
        ], [
            'extraPayment.amount.required' => 'The amount is required.',
            'extraPayment.due_date.required' => 'The due date is required.',
            'extraPayment.desc.required' => 'The description is required.',
        ]);

        try {
            ExtraPayment::createExtraPayment(
                $this->employee,
                $this->extraPayment['name'],
                $this->extraPayment['amount'],
                $this->extraPayment['due_date'],
                $this->extraPayment['desc']
            );

            $this->closeAddExtraPaymentModal();
            $this->alertSuccess('Extra payment added successfully.');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Error creating extra payment. Please try again.');
        }
    }

    // --------- Loan Delete Function ---------
    public function deleteLoan($loanId)
    {
        try {
            $loan = Loan::findOrFail($loanId);
            $loan->deleteLoan($this->employee);
            $this->alertSuccess('Loan deleted successfully.');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Error deleting loan. Please try again.');
        }
    }

    // --------- Purchase Delete Function ---------
    public function deletePurchase($purchaseId)
    {
        try {
            $purchase = Purchase::findOrFail($purchaseId);
            $purchase->deletePurchase($this->employee);
            $this->alertSuccess('Purchase deleted successfully.');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Error deleting purchase. Please try again.');
        }
    }

    // --------- Extra Payment Delete Function ---------
    public function deleteExtraPayment($extraPaymentId)
    {
        try {
            $extraPayment = ExtraPayment::findOrFail($extraPaymentId);
            $extraPayment->deleteExtraPayment();
            $this->alertSuccess('Extra payment deleted successfully.');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Error deleting extra payment. Please try again.');
        }
    }

    // --------- Extra Payment Edit Functions ---------
    public $showEditExtraPaymentModal = false;
    public $editingExtraPayment = null;
    public $editExtraPaymentDueDate = '';

    public function editExtraPayment($extraPaymentId)
    {
        try {
            $this->editingExtraPayment = ExtraPayment::findOrFail($extraPaymentId);
            $this->editExtraPaymentDueDate = $this->editingExtraPayment->due_date?->format('Y-m-d') ?? null;
            $this->showEditExtraPaymentModal = true;
        } catch (Exception $e) {
            $this->alertError('Error loading extra payment. Please try again.');
        }
    }

    public function closeEditExtraPaymentModal()
    {
        $this->showEditExtraPaymentModal = false;
        $this->editingExtraPayment = null;
        $this->editExtraPaymentDueDate = '';
    }

    public function saveExtraPaymentEdit()
    {
        $this->validate([
            'editExtraPaymentDueDate' => 'required|date',
        ], [
            'editExtraPaymentDueDate.required' => 'The due date is required.',
            'editExtraPaymentDueDate.date' => 'The due date must be a valid date.',
        ]);

        try {
            $this->editingExtraPayment->editExtraPayment($this->editExtraPaymentDueDate);
            $this->closeEditExtraPaymentModal();
            $this->alertSuccess('Extra payment due date updated successfully.');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Error updating extra payment. Please try again.');
        }
    }


    ///payroll detials modal
    // Modal properties
    public $showEmployeeDetailsModal = false;
    public $selectedEmployeeId = null;
    public $selectedPayrollEmployee = null;
    public $employeeAttendance = [];
    public $employeeBenefitPayments = [];
    public $employeeOvertimes = [];
    public $employeeExtraPayments = [];

    public function showEmployeeDetails($payrollEmployeeId)
    {
        $this->selectedPayrollEmployee = \App\Models\Benefits\Payrolls\PayrollEmployee::with('employee')
            ->findOrFail($payrollEmployeeId);

        $this->selectedEmployeeId = $this->selectedPayrollEmployee->employee_id;

        // Load attendance records for this employee and payroll
        $this->employeeAttendance = \App\Models\Attendance\Attendance::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->selectedPayrollEmployee->payroll_id)
            ->orderBy('date')
            ->get();

        // Load benefit payments for this employee and payroll
        $this->employeeBenefitPayments = \App\Models\Benefits\Payrolls\BenefitPayment::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->selectedPayrollEmployee->payroll_id)
            ->orderBy('created_at')
            ->get();

        // Load overtime records for this employee and payroll
        $this->employeeOvertimes = \App\Models\Attendance\Overtime::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->selectedPayrollEmployee->payroll_id)
            ->orderBy('date')
            ->get();

        // Load extra payments for this employee and payroll
        $this->employeeExtraPayments = \App\Models\Benefits\Payrolls\ExtraPayment::where('employee_id', $this->selectedEmployeeId)
            ->where('payroll_id', $this->selectedPayrollEmployee->payroll_id)
            ->orderBy('due_date')
            ->get();

        $this->showEmployeeDetailsModal = true;
    }

    public function closeEmployeeDetailsModal()
    {
        $this->showEmployeeDetailsModal = false;
        $this->selectedEmployeeId = null;
        $this->selectedPayrollEmployee = null;
        $this->employeeAttendance = [];
        $this->employeeBenefitPayments = [];
        $this->employeeOvertimes = [];
        $this->employeeExtraPayments = [];
    }

    public function openEditBaseInfoModal()
    {
        $this->name = $this->employee->name;
        $this->name_ar = $this->employee->name_ar;
        $this->mother_name = $this->employee->mother_name;
        $this->email = $this->employee->email;
        $this->phone = $this->employee->phone;
        $this->address = $this->employee->address;
        $this->nationality = $this->employee->nationality;
        $this->gender = $this->employee->gender;
        $this->birth_date = $this->employee->birth_date ? Carbon::parse($this->employee->birth_date)->format('Y-m-d') : null;
        $this->employment_date = $this->employee->employment_date ? Carbon::parse($this->employee->employment_date)->format('Y-m-d') : null;
        $this->termination_date = $this->employee->termination_date ? Carbon::parse($this->employee->termination_date)->format('Y-m-d') : null;
        $this->release_date = $this->employee->release_date ? Carbon::parse($this->employee->release_date)->format('Y-m-d') : null;
        $this->absent_date = $this->employee->absent_date ? Carbon::parse($this->employee->absent_date)->format('Y-m-d') : null;
        $this->editBaseInfoModal = true;
    }

    public function closeEditBaseInfoModal()
    {
        $this->editBaseInfoModal = false;
    }

    public function updateBaseInfo()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'gender' => 'required|string|in:Male,Female',
            'birth_date' => 'required|date',
            'employment_date' => 'required|date',
            'termination_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'absent_date' => 'nullable|date',
            'mother_name' => 'nullable|string|max:255',
            'release_note' => 'nullable|string|max:255',
            'id_number' => 'required|string|max:255',
        ]);

        $res = $this->employee->updateBaseInfo(
            $this->name,
            $this->name_ar,
            $this->email,
            $this->phone,
            $this->address,
            $this->nationality,
            $this->gender,
            $this->birth_date,
            $this->employment_date,
            $this->id_number,
            $this->mother_name,
            $this->termination_date ? Carbon::parse($this->termination_date) : null,
            $this->release_date ? Carbon::parse($this->release_date) : null,
            $this->absent_date ? Carbon::parse($this->absent_date) : null,
            $this->release_note
        );

        if ($res) {
            $this->closeEditBaseInfoModal();
            $this->alert('success', 'Employee updated successfully!');
        } else {
            $this->alertError();
        }
    }
}
