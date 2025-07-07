<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LabourDocument extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'labour_document';
    const DOC_TYPE = 'labourDocument';

    // Type enum constants
    const TYPE_AVAILABLE = 'Available';
    const TYPE_NOT_AVAILABLE = 'Not Available';
    const TYPE_REGISTERED = 'Registered';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        'issue_date',
        'registration_date',
        'expiry_date',
        'type',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'registration_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * Update the record with new data
     * 
     * @param string $file_path
     * @param Carbon $issue_date
     * @param Carbon|null $registration_date
     * @param Carbon|null $expiry_date
     * @param string|null $type
     * @return bool
     */
    public function updateRecord($file_path, Carbon $issue_date, ?Carbon $registration_date = null, ?Carbon $expiry_date = null, ?string $type = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this->employee->labourDocument()->first())) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'registration_date' => $registration_date,
                'expiry_date' => $expiry_date,
                'type' => $type,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            AppLog::info('Labour Document Updated', 'Labour document updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating labour document', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating labour document');
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
            AppLog::info('Labour Document Deleted', 'Labour document deleted for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting labour document', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting labour document');
        }
    }
} 