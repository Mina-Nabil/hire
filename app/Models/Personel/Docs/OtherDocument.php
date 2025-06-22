<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OtherDocument extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'other_document';
    const DOC_TYPE = 'otherDocument';

    protected $fillable = [
        'employee_id',
        'created_by',
        'name',
        'file_path',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];
    
    /**
     * Update the record with new data
     * 
     * @param string $name
     * @param string $file_path
     * @param Carbon $issue_date
     * @return bool
     */
    public function updateRecord($name, $file_path, Carbon $issue_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', [$this->employee, 'otherDocument'])) {
            throw new AppException('You dont have permission to set docs for this employee');
        }
        
        try {
            $this->update([
                'name' => $name,
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            AppLog::info('Other Document Updated', 'Other document updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating other document', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating other document');
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
            AppLog::info('Other Document Deleted', 'Other document deleted for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting other document', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting other document');
        }
    }
} 