<?php

namespace App\Policies;

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\JobOffers\JobOffer;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class JobOfferPolicy
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
    public function view(User $user, JobOffer $jobOffer): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Application $application = null): bool
    {
        return $user->is_admin || $user->is_hr 
        || ($application && ($application->vacancy->assigned_to == $user->id || $application->vacancy->hiring_manager_id == $user->id || $application->vacancy->hr_manager_id == $user->id));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobOffer $jobOffer): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobOffer $jobOffer): bool
    {
        return $user->is_admin || $user->is_hr;
    }
}
