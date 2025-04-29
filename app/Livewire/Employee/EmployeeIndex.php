<?php

namespace App\Livewire\Employee;

use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';

    public function render()
    {
        $employees = Employee::search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.employee.employee-index', [
            'employees' => $employees
        ])->layout('components.layouts.app', [
            'title' => 'Employees',
            'employeesIndex' => 'active'
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
} 