<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'city_id'];
    public $timestamps = false;

    ///relations
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    ///methods
    public static function newArea($name, $city_id)
    {
        return Area::create(['name' => $name, 'city_id' => $city_id]);
    }
}