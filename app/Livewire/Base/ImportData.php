<?php

namespace App\Livewire\Base;

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
    public $benefits = [];
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
        $this->benefits = [];
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
                $this->employees, // employees
                $this->departments, // departments
                $this->locations, // locations
                $this->positions, // positions
                $this->benefits  // benefits
            );
            
            $this->showUploadModal = false;
            $this->file = null;
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.base.import-data');
    }
}
