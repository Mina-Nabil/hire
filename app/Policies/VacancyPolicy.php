<?php

namespace App\Policies;

use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class VacancyPolicy
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
    public function view(User $user, Vacancy $vacancy): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $vacancy->assigned_to;
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
    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $vacancy->assigned_to;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->is_admin || $user->is_hr;
    }
} 