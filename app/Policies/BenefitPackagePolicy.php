<?php

namespace App\Policies;

use App\Models\Benefits\BenefitPackage;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class BenefitPackagePolicy
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
    public function view(User $user, BenefitPackage $benefitPackage): bool
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
    public function update(User $user, BenefitPackage $benefitPackage): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BenefitPackage $benefitPackage): bool
    {
        return $user->is_admin;
    }
}
