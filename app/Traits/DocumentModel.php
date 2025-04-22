<?php

namespace App\Traits;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait DocumentModel
{

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    public function getFilePathAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : "N/A";
    }

    public function getIssueDateAttribute()
    {
        return $this->issue_date ? $this->issue_date->format('Y-m-d') : "N/A";
    }

    public function getExpiryDateAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('Y-m-d') : "N/A";
    }

    public function getDaysLeftAttribute()
    {
        $now = Carbon::now();
        return $this->expiry_date ? $this->expiry_date->diffInDays($now) : null;
    }



    ////relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
