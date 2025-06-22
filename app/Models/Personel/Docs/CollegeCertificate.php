<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CollegeCertificate extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'college_certificate';
    const DOC_TYPE = 'collegeCertificate';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    /**
     * Update the record with new data
     * 
     * @param string $file_path
     * @param Carbon $issue_date
     * @return bool
     */
    public function updateRecord($file_path, Carbon $issue_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this->employee->collegeCertificate()->first())) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            AppLog::info('College Certificate Updated', 'College certificate updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating college certificate', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating college certificate');
        }
    }

    /**
     * Delete the record
     * 
     * @return bool
     */
    public function deleteRecord()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('deleteDocs', $this->employee->collegeCertificate()->first())) {
            throw new AppException('You dont have permission to delete docs for this employee');
        }

        try {
            $this->delete();
            AppLog::info('College Certificate Deleted', 'College certificate deleted for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting college certificate', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting college certificate');
        }
    }
} 