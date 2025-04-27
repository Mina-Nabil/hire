<?php

namespace App\Models\Personel\Docs;

use App\Exceptions\AppException;
use App\Traits\DocumentModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HrLetter extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'hr_letter';

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
        if (!$loggedInUser->can('setDocs', $this->employee)) {
            throw new AppException('You dont have permission to set docs for this employee');
        }

        try {
            $this->update([
                'file_path' => $file_path,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                'created_by' => Auth::id(), // Track who updated it
            ]);
            return true;
        } catch (\Exception $e) {
            report($e);
            throw new AppException('Error updating hr letter');
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
            return true;
        } catch (\Exception $e) {
            report($e);
            throw new AppException('Error deleting hr letter');
        }
    }
}
