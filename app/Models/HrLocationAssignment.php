<?php

namespace App\Models;

use App\Models\Hierarchy\Location;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrLocationAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
    ];

    /**
     * Get the user that is assigned to the location.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location that the HR user is assigned to.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
