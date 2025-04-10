<?php

namespace App\Models\Personel;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    const MORPH_NAME = 'employee';

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where(function ($query) use ($now) {
            // $query->whereNull('termination_date');
        });
    }

}
