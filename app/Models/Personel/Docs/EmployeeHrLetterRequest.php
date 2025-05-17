<?php

namespace App\Models\Personel\Docs;

use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeHrLetterRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'requested_by',
        'approved_by',
        'directed_to',
        'employee_note',
        'admin_note',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_COMPLETED,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Set the status of the HR letter request
     * 
     * @param string $status The new status
     * @param int|null $approved_by User ID of the person approving the request
     * @param string|null $admin_note Optional note from admin
     * @param string|null $file_path Path to the generated HR letter file (required for approval)
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function setStatus(string $status, ?int $approved_by = null, ?string $admin_note = null, ?string $file_path = null): bool
    {
        if (!in_array($status, self::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid status provided');
        }

        $data = ['status' => $status];
        
        if ($status === self::STATUS_APPROVED || $status === self::STATUS_COMPLETED) {
            if (!$approved_by) {
                throw new \InvalidArgumentException('Approved by user ID is required for approval or completion');
            }
            $data['approved_by'] = $approved_by;
        }

        if ($admin_note) {
            $data['admin_note'] = $admin_note;
        }

        // When approving a request, automatically generate an HR letter
        if ($status === self::STATUS_APPROVED && $file_path) {
            try {
                return DB::transaction(function () use ($data, $file_path) {
                    // First update the request status
                    $this->update($data);
                    
                    // Then generate the HR letter document
                    $result = $this->employee->setHrLetter(
                        $file_path, 
                        Carbon::now(), 
                        null // No expiry date for HR letters
                    );

                    // If HR letter was successfully created, update status to completed
                    if ($result) {
                        return $this->update(['status' => self::STATUS_COMPLETED]);
                    }
                    AppLog::error('Error creating HR letter', 'Failed to create HR letter for employee: ' . $this->employee->name, loggable: $this);
                    return true;
                });
            } catch (\Exception $e) {
                report($e);
                AppLog::error('Error creating HR letter', $e->getMessage(), loggable: $this);
                throw new \RuntimeException('Failed to generate HR letter: ' . $e->getMessage());
            }
        }

        return $this->update($data);
    }

    public function delete(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending requests can be deleted');
        }
        AppLog::info('Employee HR Letter Request Deleted', 'Request deleted for employee: ' . $this->employee->name, loggable: $this);
        return parent::delete();
    }
} 