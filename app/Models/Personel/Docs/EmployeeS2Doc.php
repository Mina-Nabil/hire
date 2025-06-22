<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmployeeS2Doc extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'employee_s2_doc';
    const DOC_TYPE = 'employeeS2Doc';

    protected $fillable = [
        'employee_id',
        'created_by',
        'file_path',
        's2_amount',
        'year',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];  

    public function deleteRecord()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('deleteDocs', $this->employee)) {
            AppLog::error('Error deleting employee s2 doc', 'User: ' . $loggedInUser->name . ' tried to delete a s2 doc for employee: ' . $this->employee->name, loggable: $this);
            throw new AppException('You dont have permission to delete docs for this employee');
        }

        try {
            $this->delete();
            AppLog::info('Employee S2 Document Deleted', 'Document deleted for employee: ' . $this->employee->name);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting employee s2 doc', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting employee s2 doc');
        }
    }
    
    public function updateRecord($file_path, $issue_date, $expiry_date, $s2_amount, $year)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this->employee->employeeS2Doc()->first())) {
            AppLog::error('Error updating employee s2 doc', 'User: ' . $loggedInUser->name . ' tried to update a s2 doc for employee: ' . $this->employee->name, loggable: $this);
            throw new AppException('You dont have permission to update docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                's2_amount' => $s2_amount,
                'year' => $year,
            ]);
            AppLog::info('Employee S2 Document Updated', 'Document updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating employee s2 doc', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating employee S2 document');
        }
    }
}   
