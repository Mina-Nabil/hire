<?php

namespace App\Policies;

use App\Models\Personel\Employee;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
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
    public function view(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can set employee docs.
     */
    public function setDocs(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }
}
