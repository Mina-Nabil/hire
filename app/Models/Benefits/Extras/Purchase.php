<?php

namespace App\Models\Benefits\Extras;

use App\Models\Personel\Employee;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    const MORPH_NAME = 'purchase';
    protected $table = 'purchases';
    protected $fillable = [
        'employee_id',
        'amount',
        'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
