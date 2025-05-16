<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Traits\DocumentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmployeeContract extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'employee_contract';

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

    public function deleteRecord()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('deleteDocs', $this->employee)) {
            AppLog::error('Error deleting employee contract', 'User: ' . $loggedInUser->name . ' tried to delete a contract for employee: ' . $this->employee->name, loggable: $this);
            throw new AppException('You dont have permission to delete docs for this employee');
        }

        try {
            $this->delete();
            AppLog::info('Employee Contract Deleted', 'Contract document deleted for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error deleting employee contract', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting employee contract');
        }
    }

    public function updateRecord($file_path, $issue_date, $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this->employee)) {
            AppLog::error('Error updating employee contract', 'User: ' . $loggedInUser->name . ' tried to update a contract for employee: ' . $this->employee->name, loggable: $this);
            throw new AppException('You dont have permission to update docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
            ]);
            AppLog::info('Employee Contract Updated', 'Contract document updated for employee: ' . $this->employee->name, loggable: $this);
            return true;
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error updating employee contract', $e->getMessage(), loggable: $this);
            throw new AppException('Error updating employee contract');
        }
    }
}
