<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'arabic_name',
    ];

    /**
     * Scope a query to search for banks by name or arabic_name
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('arabic_name', 'like', '%' . $search . '%');
        });
    }

    ///static methods
    public static function createBank($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', self::class)) throw new AppException("You are not authorized to create a bank");


        try {
            return self::firstOrCreate(
                ['name' => $name],
                ['arabic_name' => $arabic_name]
            );
            AppLog::info('Bank Created', "Name: $name, Arabic Name: $arabic_name", loggable: $bank);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating bank', $e->getMessage());
            throw new AppException("Failed to create bank");
        }
    }

    ///edit methods
    public function editBank($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) throw new AppException("You are not authorized to edit this bank");

        try {
            $this->update([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
            AppLog::info('Bank Updated', "Name: $name, Arabic Name: $arabic_name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing bank', $e->getMessage());
            throw new AppException("Failed to edit bank");
        }
    }

    ///delete methods
    public function deleteBank()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) throw new AppException("You are not authorized to delete this bank");

        try {
            $this->delete();
            AppLog::info('Bank Deleted', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting bank', $e->getMessage(), loggable: $this);
            throw new AppException("Failed to delete bank");
        }
    }
}
