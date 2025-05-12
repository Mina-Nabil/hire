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

    public function openFileUpload()
    {
        $this->showUploadModal = true;
    }
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->file = null;
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
            MigrationService::migrateFromStartupfile($this->file->getRealPath());
            $this->showUploadModal = false;
            $this->file = null;
            $this->redirect('/hierarchy/locations');
        } catch (Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.base.import-data');
    }
}
