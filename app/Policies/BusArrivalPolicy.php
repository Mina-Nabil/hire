<?php

namespace App\Policies;

use App\Models\Attendance\BusArrival;
use App\Models\Users\User;

class BusArrivalPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return $user->is_admin || $user->is_hr;
    }

    public function view(User $user, BusArrival $busArrival)
    {
        return $user->is_admin || $user->is_hr;
    }

    public function create(User $user)
    {
        return $user->is_admin || $user->is_hr;
    }

    public function update(User $user, BusArrival $busArrival)
    {
        return $user->is_admin || $user->is_hr;
    }

    public function delete(User $user, BusArrival $busArrival)
    {
        return $user->is_admin || $user->is_hr;
    }
}
