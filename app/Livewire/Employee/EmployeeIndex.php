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
            ->with('info')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.employee.employee-index', [
            'employees' => $employees
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function exportToExcel()
    {
        try {
            return Employee::exportToExcel();
        } catch (\Exception $e) {
            report($e);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function importEmployees()
    {
        return redirect()->route('employees.import');
    }
} 