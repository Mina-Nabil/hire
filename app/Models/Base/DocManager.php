<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DocManager extends Model
{
    const MORPH_NAME = 'doc_manager';
    
    protected $fillable = [
        'doc_type',
        'name', 
        'description',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    ///scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('doc_type', 'like', '%' . $search . '%')
                     ->orWhere('description', 'like', '%' . $search . '%');
    }

    ///methods
    public static function newDocManager($doc_type, $name, $description = null, $is_required = true, $is_active = true)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', DocManager::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $docManager = DocManager::create([
                'doc_type' => $doc_type,
                'name' => $name,
                'description' => $description,
                'is_required' => $is_required,
                'is_active' => $is_active
            ]);
            AppLog::info('Document Manager Created', "Type: $doc_type, Name: $name", loggable: $docManager);
            return $docManager;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating document manager', $e->getMessage());
            throw new AppException('Error creating document manager');
        }
    }
    
    public function updateDocManager($name, $description = null, $is_required = null, $is_active = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $updateData = ['name' => $name];
            
            if ($description !== null) {
                $updateData['description'] = $description;
            }
            
            if ($is_required !== null) {
                $updateData['is_required'] = $is_required;
            }
            
            if ($is_active !== null) {
                $updateData['is_active'] = $is_active;
            }

            $this->update($updateData);
            AppLog::info('Document Manager Updated', "Type: $this->doc_type, Name: $name", loggable: $this);
            return $this;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating document manager', $e->getMessage());
            throw new AppException('Error updating document manager');
        }
    }
    
    public function deleteDocManager()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $this->delete();
            AppLog::info('Document Manager Deleted', "Type: $this->doc_type, Name: $this->name");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting document manager', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting document manager');
        }
    }

    /**
     * Get document type by identifier
     */
    public static function getByType($doc_type)
    {
        return static::where('doc_type', $doc_type)->first();
    }

    /**
     * Check if a document type is required
     */
    public static function isRequired($doc_type)
    {
        $doc = static::getByType($doc_type);
        return $doc ? $doc->is_required : false;
    }

    /**
     * Check if a document type is active
     */
    public static function isActive($doc_type)
    {
        $doc = static::getByType($doc_type);
        return $doc ? $doc->is_active : false;
    }
} 