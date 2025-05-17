<?php

namespace App\Models\Hierarchy;

use App\Exceptions\AppException;
use App\Models\HrLocationAssignment;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Location extends Model
{
    const MORPH_NAME = 'location';

    protected $fillable = ['name', 'latitude', 'longitude'];
    public $timestamps = false;

    protected $appends = ['map_link'];

    public function getMapLinkAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
    
    /**
     * Get the HR assignments for this location.
     */
    public function hrAssignments(): HasMany
    {
        return $this->hasMany(HrLocationAssignment::class);
    }
    
    /**
     * Get the HR users assigned to this location.
     */
    public function assignedHrUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hr_location_assignments');
    }

    
    
    ////static functions
    public static function createLocation(string $name, ?float $latitude = null, ?float $longitude = null): Location
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('create', Location::class)) {
            AppLog::error('Error creating location', 'User: ' . $loggerInUser->name . ' tried to create a location', loggable: $loggerInUser);
            throw new AppException('You are not authorized to create a location');
        }

            try {
                $location = self::create([
                'name' => $name,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            AppLog::info('Location Created', "Name: $name", loggable: $location);
            return $location;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating location', $e->getMessage());
            throw new AppException('Failed to create location');
        }
    }


    ////model functions
    public function editInfo(string $name, ?float $latitude = null, ?float $longitude = null): bool
    {
        /** @var User $loggerInUser */
        $loggerInUser = Auth::user();
        if (!$loggerInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this location');
        }

            try {
                $location = $this->update([
                'name' => $name,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            AppLog::info('Location Updated', "Name: $name", loggable: $location);
            return $location;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing location', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to edit location');
        }
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

        try {
            $this->delete();
            AppLog::info('Location Deleted', "Name: $this->name");
            return true;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting location', $e->getMessage(), loggable: $this);
            throw new AppException('Failed to delete location');
        }
    }

    /**
     * Assign HR users to this location.
     * 
     * @param array $userIds Array of HR user IDs to assign to the location
     * @return void
     * @throws AppException If the user is not authorized to update assignments
     */
    public function setAssignedHrUsers(array $userIds): void
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update HR user assignments for this location');
        }

        try {
            // Use sync to efficiently manage the pivot relationship
            // This will add new assignments, keep existing ones, and remove those not in the array
            $this->assignedHrUsers()->sync($userIds);
            AppLog::info('HR Users Assigned to Location', "Location: $this->name, changed assignments", loggable: $this);
        } catch (\Exception $e) {
            report($e);
            AppLog::error('Error assigning HR users to location', $e->getMessage(), loggable: $this);
            throw new AppException('Error assigning HR users: ' . $e->getMessage());
        }
    }

    protected static function booted()
    {
        static::addGlobalScope('hrAccessibleLocations', function ($builder) {
            $user = Auth::user();
            
            // If no user is logged in or if they are admin, don't restrict
            if (!$user || $user->is_admin) {
                return;
            }
            
            // If user is HR, restrict to their assigned locations
            if ($user->is_hr) {
                // Use direct query to avoid infinite loop
                $locationIds = HrLocationAssignment::where('user_id', $user->id)
                    ->pluck('location_id')
                    ->toArray();
                
                // Only apply filter if the user has assigned locations
                if (!empty($locationIds)) {
                    $builder->whereIn('locations.id', $locationIds);
                }
            }
        });
    }
    
    /**
     * Get all locations without the HR accessibility scope.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutHrScope()
    {
        return static::withoutGlobalScope('hrAccessibleLocations');
    }
}
