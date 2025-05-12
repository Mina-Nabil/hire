<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Location extends Model
{
    const MORPH_NAME = 'location';

    protected $fillable = ['name'];
    public $timestamps = false;
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    ////static functions
    public static function createLocation(string $name): Location
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('create', Location::class)) {
            throw new AppException('You are not authorized to create a location');
        }

        return self::create([
            'name' => $name,
        ]);
    }


    ////model functions
    public function editInfo(string $name): bool
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this location');
        }

        return $this->update([
            'name' => $name,
        ]);
    }


    public function deleteLocation(): bool
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('delete', $this)) {
            throw new AppException('You are not authorized to delete this location');
        }

        if ($this->positions()->count() > 0) {
            throw new AppException('This location has positions and cannot be deleted');
        }

        return $this->delete();
    }
}
