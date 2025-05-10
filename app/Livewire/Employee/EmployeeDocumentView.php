<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Personel\Employee;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.employee')]
#[Title('My Documents')]
class EmployeeDocumentView extends EmployeeShow
{
    public function mount($id = null)
    {
        // Get the authenticated employee
        $employee = Employee::where('user_id', Auth::id())->first();
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')->with('error', 'Employee record not found.');
        }
        
        // Call parent mount with the employee's ID
        parent::mount($employee->id);
        
        // Set active section to Documents
        $this->changeSection('documents');
    }

    public function render()
    {
        return view('livewire.employee.employee-document-view', [
            'documents' => 'active',
        ]);
    }
} 