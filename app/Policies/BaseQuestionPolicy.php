<?php

namespace App\Policies;

use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class BaseQuestionPolicy
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
    public function view(User $user, BaseQuestion $baseQuestion): bool
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
    public function update(User $user, BaseQuestion $baseQuestion): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BaseQuestion $baseQuestion): bool
    {
        return $user->is_admin || $user->is_hr;
    }
} 