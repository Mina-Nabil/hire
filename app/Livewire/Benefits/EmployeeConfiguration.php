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

class EmployeeConfiguration extends Component
{
    use AlertFrontEnd, WithPagination;
    
    public Employee $employee;
    public $activeTab = 'info';

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

    public function editConfiguration()
    {
        $this->dispatch('editConfiguration', employeeId: $this->employee->id);
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
          ->whereNull('end_date')
          ->get();

      // Load vacation benefits - using VacationBenefit instead of VacationDetail
      $employeeVacations = VacationBenefit::where('employee_id', $this->employee->id)
          ->whereNull('end_date')
          ->get();

      // Load payments
      $employeePayments = BenefitPayment::where('employee_id', $this->employee->id)
          ->orderBy('created_at', 'desc')
          ->paginate(10);

      $extraPayments = ExtraPayment::where('employee_id', $this->employee->id)
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
                Carbon::parse($this->vacationBenefit['start_date'])
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
}
