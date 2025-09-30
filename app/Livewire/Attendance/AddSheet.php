<?php

namespace App\Livewire\Attendance;

use App\Exceptions\AppException;
use App\Models\Attendance\Attendance;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddSheet extends Component
{
    use WithFileUploads, AlertFrontEnd;

    public $showUploadModal = false;
    public $showDownloadModal = false;
    public $file;
    public $uploading = false;
    public $fileType = 'standard'; // 'standard' or 'zkteco'

    public $uploadedAttendance = [];
    public $zktecoResults = [];

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
        $this->reset(['file', 'uploading', 'fileType']);
    }

    public function uploadSheet()
    {
        $this->validate();

        try {
            $this->uploading = true;
            
            if ($this->fileType === 'zkteco') {
                // Process ZKTeco device file
                $this->zktecoResults = Attendance::processZKTecoAttendanceFile($this->file->getRealPath());
                $this->closeUploadModal();
                
                $message = sprintf(
                    'ZKTeco attendance file processed successfully! Created %d punches for %d dates.',
                    $this->zktecoResults['punches_created'],
                    count($this->zktecoResults['dates_processed'])
                );
                
                if (!empty($this->zktecoResults['errors'])) {
                    $message .= ' ' . count($this->zktecoResults['errors']) . ' errors encountered.';
                }
                
                $this->alertSuccess($message);
            } else {
                // Process standard attendance file
                $this->uploadedAttendance = Attendance::getUploadedAttendance($this->file->getRealPath());
                $this->closeUploadModal();
                $this->alertSuccess('Attendance sheet processed successfully!');
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        } finally {
            $this->uploading = false;
        }
    }

    public function clearUploadedAttendance()
    {
        $this->uploadedAttendance = [];
        $this->zktecoResults = [];
    }

    public function saveAttendance()
    {
        try {
            Attendance::saveAttendance($this->uploadedAttendance);
            $this->alertSuccess('Attendance saved successfully!');
            $this->clearUploadedAttendance();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            $this->alertError('Failed to save attendance: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Attendance::downloadTemplate();
    }

    public function render()
    {
        return view('livewire.attendance.add-sheet');
    }
}
