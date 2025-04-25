<?php

namespace App\Models\Personel\Docs;

use App\Traits\DocumentModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PoliceRecord extends Model
{
    use DocumentModel;

    const MORPH_NAME = 'police_record';

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
            return false;
        }
    }
}
