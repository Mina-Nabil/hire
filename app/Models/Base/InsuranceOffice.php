<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InsuranceOffice extends Model
{
    protected $fillable = [
        'name',
        'arabic_name',
    ];

    const MORPH_NAME = 'insurance_office';

    /**
     * Scope a query to search for insurance offices by name or arabic_name
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
    public static function createInsuranceOffice($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', self::class)) throw new AppException("You are not authorized to create an insurance office");

        try {
            return self::create([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
            AppLog::info('Insurance Office Created', "Name: $name", loggable: $insuranceOffice);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating insurance office', $e->getMessage());
            throw new AppException("Failed to create insurance office");
        }
    }

    ///edit methods
    public function editInsuranceOffice($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) throw new AppException("You are not authorized to edit this insurance office");

        try {
            $this->update([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
            AppLog::info('Insurance Office Updated', "Name: $name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing insurance office', $e->getMessage(), loggable: $this);
            throw new AppException("Failed to edit insurance office");
        }
    }

    ///delete methods
    public function deleteInsuranceOffice()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) throw new AppException("You are not authorized to delete this insurance office");

        try {
            $this->delete();
            AppLog::info('Insurance Office Deleted', "Name: $this->name");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting insurance office', $e->getMessage(), loggable: $this);
            throw new AppException("Failed to delete insurance office");
        }
    }

}
