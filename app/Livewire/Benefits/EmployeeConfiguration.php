<?php

namespace App\Livewire\Benefits;

use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Extras\Loan;
use App\Models\Benefits\Extras\Purchase;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class EmployeeConfiguration extends Component
{
    use AlertFrontEnd;

    // Tabs control
    public $activeTab = 'info';
    public $employee;
    public $employeeBenefits = [];
    public $employeeVacations = [];
    public $employeePayments = [];
    public $appliedVacations = [];
    public $gainedVacations = [];
    public $loans = [];
    public $purchases = [];

    public function mount($employee)
    {
        $this->employee = Employee::findOrFail($employee);
        $this->loadEmployeeDetails();
    }

    public function loadEmployeeDetails()
    {
        // Load employee with all related benefit data
        $this->employee->load([
            'benefitConfiguration.benefitPackage',
            'baseBenefits',
            'vacationBenefits',
            'appliedVacations',
            'gainedVacations',
            'loans',
            'purchases'
        ]);

        // Transform the data for display
        $this->employeeBenefits = $this->employee->baseBenefits;
        $this->employeeVacations = $this->employee->vacationBenefits;
        
        // Load payment data
        $this->employeePayments = BenefitPayment::where('employee_id', $this->employee->id)
            ->with(['baseBenefit'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load vacation data
        $this->appliedVacations = $this->employee->appliedVacations()
            ->with(['vacationBenefit'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->gainedVacations = $this->employee->gainedVacations()
            ->with(['vacationBenefit'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load loans and purchases
        $this->loans = $this->employee->loans()
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->purchases = $this->employee->purchases()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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

    public function getAttendanceCalculationLabel($type)
    {
        $types = [
            'flexible' => 'Flexible',
            'semi-flexible' => 'Semi-Flexible',
            'fixed' => 'Fixed'
        ];

        return $types[$type] ?? $type;
    }

    public function render()
    {
        return view('livewire.benefits.employee-configuration');
    }
}
