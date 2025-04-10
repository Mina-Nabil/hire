<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Users\User;
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

        return City::create(['name' => $name]);
    }
    
    public function updateCity($name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        return $this->update(['name' => $name]);
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
            throw new AppException(__('areas.city_has_areas'));
        }
        
        return $this->delete();
    }
}
