<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Area extends Model
{
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

        return Area::create(['name' => $name, 'city_id' => $city_id]);
    }
    
    public function updateArea($name, $city_id)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        return $this->update([
            'name' => $name,
            'city_id' => $city_id
        ]);
    }
    
    public function deleteArea()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException(__('misc.not_authorized'));
        }

        return $this->delete();
    }
}