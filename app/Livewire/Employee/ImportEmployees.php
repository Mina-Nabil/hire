<?php

namespace App\Livewire\Employee;

use App\Services\MigrationService;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportEmployees extends Component
{
    use WithFileUploads, AlertFrontEnd;

    public $file;
    public $showUploadModal = false;
    public $employeeData = [];
    public $activeTab = 'new_employees';

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls|max:10240', // 10MB max
    ];

    public function render()
    {
        return view('livewire.employee.import-employees');
    }

    public function openFileUpload()
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->file = null;
        $this->resetValidation();
    }

    public function downloadTemplate()
    {
        // This will use the existing exportToExcel function as a template
        try {
            return \App\Models\Personel\Employee::exportToExcel();
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to download template: ' . $e->getMessage());
        }
    }

    public function uploadSheet()
    {
        $this->validate();

        try {
            // Load and validate employee data using MigrationService
            $this->employeeData = MigrationService::LoadEmployeeData($this->file->getRealPath());

            // Close modal and show results
            $this->showUploadModal = false;
            $this->file = null;

            // Set active tab to the first non-empty category
            if (!empty($this->employeeData['new_employees'])) {
                $this->activeTab = 'new_employees';
            } elseif (!empty($this->employeeData['updated_employees'])) {
                $this->activeTab = 'updated_employees';
            } elseif (!empty($this->employeeData['errors'])) {
                $this->activeTab = 'errors';
            }

            $this->alert('success', 'File processed successfully! Found ' . 
                $this->employeeData['summary']['new_count'] . ' new employees, ' .
                $this->employeeData['summary']['update_count'] . ' updates, and ' .
                $this->employeeData['summary']['error_count'] . ' errors.');

        } catch (\Exception $e) {
            report($e);
            $this->alert('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function importData()
    {
        try {
            // Check if we have valid data to import
            if (empty($this->employeeData) || 
                (empty($this->employeeData['new_employees']) && empty($this->employeeData['updated_employees']))) {
                $this->alert('warning', 'No valid data to import. Please upload and process a file first.');
                return;
            }

            // Check if there are any errors that need to be resolved first
            if (!empty($this->employeeData['errors'])) {
                $errorCount = count($this->employeeData['errors']);
                $this->alert('warning', "Please resolve {$errorCount} error(s) in your data before importing. Check the Errors tab for details.");
                return;
            }

            // Save the employee data using MigrationService
            $results = MigrationService::SaveEmployeeData($this->employeeData);

            // Clear the loaded data after successful import
            $this->employeeData = [];
            $this->activeTab = 'new_employees';

            // Show success message with results
            $message = 'Import completed successfully! ';
            if ($results['created_count'] > 0) {
                $message .= "Created {$results['created_count']} new employees. ";
            }
            if ($results['updated_count'] > 0) {
                $message .= "Updated {$results['updated_count']} existing employees. ";
            }
            if (!empty($results['errors'])) {
                $errorCount = count($results['errors']);
                $message .= "However, {$errorCount} row(s) had errors during processing.";
                $this->alert('warning', $message);
            } else {
                $this->alert('success', $message);
            }

        } catch (\Exception $e) {
            report($e);
            $this->alert('error', 'Failed to import data: ' . $e->getMessage());
        }
    }

    public function clearData()
    {
        $this->employeeData = [];
        $this->activeTab = 'new_employees';
        $this->alert('success', 'Data cleared successfully');
    }
} 