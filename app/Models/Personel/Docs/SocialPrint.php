<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SocialPrint extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'social_print';

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
        if (!$loggedInUser->can('setDocs', $this->employee)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            AppLog::info('Social Print Updated', 'Social print updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating social print', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating social print');
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
        if (!$loggedInUser->can('deleteDocs', $this->employee)) {
            throw new AppException('You dont have permission to delete docs for this employee');
        }

        try {
            $this->delete();
            AppLog::info('Social Print Deleted', 'Social print deleted for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting social print', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting social print');
        }
    }
} 