<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;


    ///relations
    public function areas()
    {
        return $this->hasMany(Area::class);
    }



    ///methods
    public static function newCity($name)
    {
        return City::create(['name' => $name]);
    }
}
