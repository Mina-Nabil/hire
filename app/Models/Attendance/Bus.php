<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Personel\Employee;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bus extends Model
{

    const MORPH_NAME = 'bus';

    protected $fillable = ['name'];

    public function arrivals()
    {
        return $this->hasMany(BusArrival::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, BenefitConfiguration::class);
    }

    /**
     * Scope a query to search for buses by name
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('name', 'like', '%' . $search . '%');
    }

    ///static methods
    public static function createBus($name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', self::class)) {
            throw new AppException("You are not authorized to create a bus");
        }

        try {
            $bus = self::firstOrCreate(['name' => $name]);
            AppLog::info('Bus Created', "Name: $name", loggable: $bus);
            return $bus;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating bus', $e->getMessage());
            throw new AppException("Failed to create bus");
        }
    }

    ///edit methods
    public function editBus($name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException("You are not authorized to edit this bus");
        }

        try {
            $this->update(['name' => $name]);
            AppLog::info('Bus Updated', "Name: $name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing bus', $e->getMessage());
            throw new AppException("Failed to edit bus");
        }
    }

    ///delete methods
    public function deleteBus()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException("You are not authorized to delete this bus");
        }

        if ($this->arrivals()->count() > 0) {
            throw new AppException("Cannot delete bus with arrivals");
        }

        try {
            $this->delete();
            AppLog::info('Bus Deleted', "Name: $this->name", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting bus', $e->getMessage(), loggable: $this);
            throw new AppException("Failed to delete bus");
        }
    }
}
