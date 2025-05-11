<?php

namespace App\Models\Personel\Docs;

use App\Models\Personel\Employee;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHrLetterRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'requested_by',
        'approved_by',
        'purpose',
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

    public function setStatus(string $status, ?int $approved_by = null, ?string $admin_note = null): bool
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

        return $this->update($data);
    }

    public function delete(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException('Only pending requests can be deleted');
        }

        return parent::delete();
    }
} 