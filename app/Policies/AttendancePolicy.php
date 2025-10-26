<?php

namespace App\Policies;

use App\Models\Attendance\Attendance;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->is_admin || ($user->is_hr && !$user->permit_tibian);
    }

    public function approve(User $user, Attendance $attendance): bool
    {
        return ($user->employee?->is_manager &&
            $attendance->employee &&
            $attendance->employee->benefitConfiguration &&
            $attendance->employee->benefitConfiguration->manager_id === $user->employee_id) ||
            $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->is_admin || $user->is_hr;
    }
}
