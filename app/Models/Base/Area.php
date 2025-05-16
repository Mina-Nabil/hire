<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Area extends Model
{
    const MORPH_NAME = 'area';
    protected $fillable = ['name', 'city_id'];
    public $timestamps = false;

    ///relations
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    ///scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhereHas('city', function($q) use ($search) {
                         $q->where('name', 'like', '%' . $search . '%');
                     });
    }

    ///methods
    public static function newArea($name, $city_id)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', Area::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $area = Area::create(['name' => $name, 'city_id' => $city_id]);
            AppLog::info('Area Created', "Name: $name, City: $city_id", loggable: $area);
            return $area;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating area', $e->getMessage());
            throw new AppException('Error creating area');
        }
    }
    
    public function updateArea($name, $city_id)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $this->update([
                'name' => $name,
                'city_id' => $city_id
            ]);
            AppLog::info('Area Updated', "Name: $name, City: $city_id", loggable: $this);
            return $this;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating area', $e->getMessage());
            throw new AppException('Error updating area');
        }
    }
    
    public function deleteArea()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $this->delete();
            AppLog::info('Area Deleted', "Name: $this->name");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting area', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting area');
        }
    }
}