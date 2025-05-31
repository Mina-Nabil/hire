<?php

namespace App\Livewire\Attendance;

use App\Exceptions\AppException;
use App\Models\Attendance\BusArrival;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddBusArrivals extends Component
{
    use WithFileUploads, AlertFrontEnd;

    public $showUploadModal = false;
    public $showDownloadModal = false;
    public $file;
    public $uploading = false;

    public $uploadedBusArrivals = [];

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls|max:20480', // 20MB Max
    ];

    public function openFileUpload()
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['file', 'uploading']);
    }

    public function uploadSheet()
    {
        $this->validate();

        try {
            $this->uploading = true;
            $this->uploadedBusArrivals = BusArrival::getUploadedBusArrival($this->file->getRealPath());
            $this->closeUploadModal();
            $this->alertSuccess('Bus arrival sheet processed successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        } finally {
            $this->uploading = false;
        }
    }

    public function clearUploadedBusArrivals()
    {
        $this->uploadedBusArrivals = [];
    }

    public function saveBusArrivals()
    {
        try {
            BusArrival::saveBusArrival($this->uploadedBusArrivals);
            $this->alertSuccess('Bus arrivals saved successfully!');
            $this->clearUploadedBusArrivals();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            $this->alertError('Failed to save bus arrivals: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return BusArrival::downloadTemplate();
    }

    public function render()
    {
        return view('livewire.attendance.add-bus-arrivals');
    }
}
