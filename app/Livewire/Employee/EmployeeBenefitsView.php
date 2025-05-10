<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Personel\Employee;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Extras\Loan;
use App\Models\Benefits\Extras\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Breadcrumb;

#[Layout('components.layouts.employee')]
#[Title('Employee Benefits')]

class EmployeeBenefitsView extends Component
{
    public $employee;
    public $employeeBenefits;
    public $employeeVacations;
    public $employeePayments;
    public $loans;
    public $purchases;
    public $appliedVacations;
    public $gainedVacations;
    public $activeTab = 'info';

    public function mount()
    {
        $this->employee = Employee::where('user_id', Auth::id())->first();
        if ($this->employee) {
            $this->loadEmployeeData();
        }
    }

    public function loadEmployeeData()
    {
        // Load base benefits
        $this->employeeBenefits = $this->employee->baseBenefits()
            ->whereNull('end_date')
            ->get();

        // Load vacation benefits
        $this->employeeVacations = $this->employee->vacationBenefits()
            ->whereNull('end_date')
            ->get();

        // Load benefit payments - using correct relationship
        $this->employeePayments = BenefitPayment::where('employee_id', $this->employee->id)
            ->latest()
            ->get();

        // Load loans
        $this->loans = $this->employee->loans()
            ->latest()
            ->get();

        // Load purchases
        $this->purchases = $this->employee->purchases()
            ->latest()
            ->get();

        // Load applied vacations
        $this->appliedVacations = $this->employee->appliedVacations()
            ->with(['vacationBenefit'])
            ->latest()
            ->get();

        // Load gained vacations
        $this->gainedVacations = $this->employee->gainedVacations()
            ->with(['vacationBenefit'])
            ->latest()
            ->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getBenefitTypeLabel($type)
    {
        return match ($type) {
            BaseBenefit::TYPE_MONTHLY => 'Monthly',
            BaseBenefit::TYPE_WEEKLY => 'Weekly',
            BaseBenefit::TYPE_QUARTERLY => 'Quarterly',
            BaseBenefit::TYPE_YEARLY => 'Yearly',
            BaseBenefit::TYPE_DAILY => 'Daily',
            default => $type,
        };
    }

    public function getReceiverLabel($receiver)
    {
        return match ($receiver) {
            'employee' => 'Employee',
            'company' => 'Company',
            'insurance' => 'Insurance',
            'taxes' => 'Taxes',
            'medical' => 'Medical',
            'other' => 'Other',
            default => $receiver,
        };
    }

    public function getAttendanceCalculationLabel($calculation)
    {
        return match ($calculation) {
            'flexible' => 'Flexible',
            'semi-flexible' => 'Semi-Flexible',
            'fixed' => 'Fixed',
            default => $calculation,
        };
    }

    public function render()
    {
        return view('livewire.employee.employee-benefits-view')->layout('components.layouts.employee', [ 'benefits' => 'active']);
    }
} 