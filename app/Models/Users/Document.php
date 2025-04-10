<?php

namespace App\Models\Users;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    const MORPH_NAME = 'document';

    const APPLICANT_DOCUMENTS = 'applicant-documents';

    protected $table = 'app_documents';
    
    protected $fillable = [
        'name',
        'file_path',
        'notes',
    ];

    /**
     * Get the applicant that owns this document.
     */
    public function docOwner(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullPathAttribute()
    {
        return Storage::disk('s3')->url($this->file_path);
    }

    ///static methods
    public static function createDocument(Model $docOwner, $name, $file_path, $notes=null)
    {
        $newDoc = new self();
        $newDoc->name = $name;
        $newDoc->file_path = $file_path;
        $newDoc->notes = $notes;
        $newDoc->docOwner()->associate($docOwner);
        try {
            $newDoc->save();
            return $newDoc;
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to create document");
        }
    }
} 