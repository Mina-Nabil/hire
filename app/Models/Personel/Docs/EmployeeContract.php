<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
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
            throw new AppException('You dont have permission to delete docs for this employee');
        }

        try {
            $this->delete();
            return true;
        } catch (\Exception $e) {
            report($e);
            throw new AppException('Error deleting employee contract');
        }
    }

    public function updateRecord($file_path, $issue_date, $expiry_date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('setDocs', $this->employee)) {
            throw new AppException('You dont have permission to update docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
            ]);
            return true;
        } catch (\Exception $e) {
            report($e);
            throw new AppException('Error updating employee contract');
        }
    }
}
