<?php

namespace App\Livewire\Benefits;

use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\BenefitPackage;
use App\Models\Personel\Employee;
use App\Models\Hierarchy\Department;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ConfigurationIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    //filters
    public $departments = [];
    public $packages = [];

    public $search = '';
    public $showFilters = false;
    public $startDate = '';
    public $endDate = '';
    public $packageId = '';
    public $departmentId = '';
    public $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'packageId' => ['except' => ''],
        'departmentId' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount()
    {
        $this->departments = Department::all();
        $this->packages = BenefitPackage::all();
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'packageId', 'departmentId', 'status']);
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedPackageId()
    {
        $this->resetPage();
    }

    public function updatedDepartmentId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::query()
            ->with(['benefitConfiguration.package', 'positions.department'])
            ->search($this->search, $this->startDate ? Carbon::parse($this->startDate) : null, $this->endDate ? Carbon::parse($this->endDate) : null, $this->packageId, $this->departmentId);

        $employees = $query->paginate(10);

        return view('livewire.benefits.configuration-index', [
            'employees' => $employees
        ])->layout('components.layouts.app', [
            'title' => 'Benefit Configurations',
            'description' => 'Manage employee benefit configurations and their associated packages',
            'configurationIndex' => 'active',
        ]);
    }
} 