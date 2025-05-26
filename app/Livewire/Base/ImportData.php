<?php

namespace App\Livewire\Base;

use App\Jobs\StartMigrationJob;
use App\Services\MigrationService;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ImportData extends Component
{
    use WithFileUploads, AlertFrontEnd;

    public $showUploadModal = false;
    public $file;
    public $employees = [];
    public $departments = [];
    public $locations = [];
    public $positions = [];
    public $salary_grades = [];
    public $activeTab = 'employees';

    public function openFileUpload()
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->file = null;
        $this->employees = [];
        $this->departments = [];
        $this->locations = [];
        $this->positions = [];
        $this->salary_grades = [];
    }

    public function downloadTemplate()
    {
        return MigrationService::downloadTemplate();
    }

    public function uploadSheet()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        try {
            MigrationService::migrateFromStartupfile(
                $this->file->getRealPath(),
                $this->locations, // locations
                $this->departments, // departments
                $this->employees, // employees
                $this->salary_grades, // salary grades
                $this->positions, // positions
            );
            
            $this->showUploadModal = false;
            $this->file = null;
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function importData()
    {
        try {   
            StartMigrationJob::dispatch($this->locations, $this->departments, $this->employees, $this->salary_grades, $this->positions);
            $this->alertSuccess('Data imported started, please check the logs for more details');
            $this->locations = [];
            $this->departments = [];
            $this->employees = [];
            $this->salary_grades = [];
            $this->positions = [];
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getMigrationResults()
    {
        return [
            'locations' => $this->locations,
            'departments' => $this->departments,
            'salary_grades' => $this->salary_grades,
            'positions' => $this->positions,
            'employees' => $this->employees,
        ];
    }

    public function render()
    {
        return view('livewire.base.import-data', [
            'migrationResults' => $this->getMigrationResults()
        ]);
    }
}
