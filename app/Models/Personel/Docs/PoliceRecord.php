<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PoliceRecord extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'police_record';
    const DOC_TYPE = 'policeRecord';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Update the record with new data
     * 
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon|null $expiry_date
     * @return bool
     */
    public function updateRecord($file_path, Carbon $issue_date, ?Carbon $expiry_date = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', [$this->employee, 'policeRecord'])) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            AppLog::info('Police Record Updated', 'Police record updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating police record', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating police record');
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
            AppLog::info('Police Record Deleted', 'Police record deleted for employee: ' . $this->employee->name);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting police record', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting police record');
        }
    }
}
