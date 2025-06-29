<?php

namespace App\Policies;

use App\Models\Recruitment\Interviews\Interview;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class InterviewPolicy
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
    public function view(User $user, Interview $interview): bool
    {
        return $user->is_admin || $user->is_hr ;
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
    public function update(User $user, Interview $interview): bool
    {
        $vacancy = $interview->application->vacancy;
        return $user->is_admin || $user->is_hr || $user->id == $vacancy->assigned_to || $user->id == $vacancy->hiring_manager_id || $user->id == $vacancy->hr_manager_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Interview $interview): bool
    {
        return $user->is_admin || $user->is_hr;
    }
}
