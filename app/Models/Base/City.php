<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class City extends Model
{
    const MORPH_NAME = 'city';
    
    protected $fillable = ['name'];
    public $timestamps = false;


    ///relations
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    ///scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    ///methods
    public static function newCity($name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', City::class)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $city = City::create(['name' => $name]);
            AppLog::info('City Created', "Name: $name", loggable: $city);
            return $city;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating city', $e->getMessage());
            throw new AppException('Error creating city');
        }
    }
    
    public function updateCity($name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        try {
            $this->update(['name' => $name]);
            AppLog::info('City Updated', "Name: $name", loggable: $this);
            return $this;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error updating city', $e->getMessage());
            throw new AppException('Error updating city');
        }
    }
    
    public function deleteCity()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        // First check if there are any areas associated with this city
        if ($this->areas()->count() > 0) {
            AppLog::error('City has areas', "Name: $this->name", loggable: $this);
            throw new AppException(__('areas.city_has_areas'));
        }

        try {
            $this->delete();
            AppLog::info('City Deleted', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting city', $e->getMessage(), loggable: $this);
            throw new AppException('Error deleting city');
        }
    }
}
