<?php

namespace App\Traits;

use App\Exceptions\AppException;
use App\Models\Base\DocManager;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait DocumentModel
{
    /**
     * Get the document name from DocManager based on DOC_TYPE constant
     * 
     * @return string|null
     */
    public function getDocumentNameAttribute()
    {
        if (defined('static::DOC_TYPE') && static::DOC_TYPE) {
            $docManager = DocManager::where('doc_type', static::DOC_TYPE)->first();
            return $docManager ? $docManager->name : null;
        }
        return null;
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    // public function getFilePathAttribute()
    // {
    //     return $this->file_path ? Storage::url($this->file_path) : "N/A";
    // }

    // public function getIssueDateAttribute()
    // {
    //     return $this->issue_date ? $this->issue_date->format('Y-m-d') : "N/A";
    // }

    // public function getExpiryDateAttribute()
    // {
    //     return $this->expiry_date ? $this->expiry_date->format('Y-m-d') : "N/A";
    // }

    /**
     * Download the document file
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     */
    public function downloadFile()
    {
        try {
            if (!$this->file_path) {
                throw new AppException('No file available for download');
            }
            
            $filePath = str_replace('storage/', '', $this->getRawOriginal('file_path'));
            
            if (!Storage::disk('s3')->exists($filePath)) {
                throw new AppException('File not found on storage');
            }
            
            $fileName = basename($filePath);
            
            return Storage::disk('s3')->download($filePath, $fileName);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getFilePathAttribute($value)
    {
        return $value ? Storage::disk('s3')->url(str_replace('storage/', '', $value)) : "N/A";
    }

    public function getIssueDateAttribute($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : "N/A";
    }

    public function getExpiryDateAttribute($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : null;
    }

    public function getDaysLeftAttribute()
    {
        $now = Carbon::now();
        return $this->expiry_date ? $this->expiry_date->diffInDays($now) : null;
    }

    /**
     * Check if the document is currently valid
     * 
     * @return bool
     */
    public function isValid()
    {
        $now = Carbon::now();
        
        // Document is valid if today is after or equal to issue date
        $isAfterIssueDate = $this->issue_date ? $now->greaterThanOrEqualTo($this->issue_date) : true;
        
        // Document is valid if today is before or equal to expiry date (or if there's no expiry date)
        $isBeforeExpiryDate = $this->expiry_date ? $now->lessThanOrEqualTo($this->expiry_date) : true;
        
        return $isAfterIssueDate && $isBeforeExpiryDate;
    }

    public function isExpired()
    {
        $now = Carbon::now();
        return $this->expiry_date ? $now->greaterThan($this->expiry_date) : false;
    }

    public function isExpiringSoon()
    {
        $now = Carbon::now();
        return $this->expiry_date ? $now->diffInDays($this->expiry_date) <= 30 : false;
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
