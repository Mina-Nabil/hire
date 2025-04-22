<?php

namespace App\Policies;

use App\Models\Base\InsuranceOffice;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class InsuranceOfficePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InsuranceOffice $insuranceOffice): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InsuranceOffice $insuranceOffice): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InsuranceOffice $insuranceOffice): bool
    {
        return $user->is_admin;
    }
}
