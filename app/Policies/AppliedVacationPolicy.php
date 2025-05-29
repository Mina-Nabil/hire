<?php

namespace App\Policies;

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class AppliedVacationPolicy
{


    /**
     * Determine whether the user can approve the applied vacation.
     */
    public function approve(User $user, AppliedVacation $appliedVacation): bool
    {
        return $appliedVacation->status !== AppliedVacation::STATUS_APPROVED &&
            ($user->is_admin || $user->is_hr || ($user->employee_id === $appliedVacation->employee->manager_id && $appliedVacation->status === AppliedVacation::STATUS_PENDING));
    }

    /**
     * Determine whether the user can reject the applied vacation.
     */
    public function reject(User $user, AppliedVacation $appliedVacation): bool
    {
        return $user->is_admin || $user->is_hr || ($user->employee_id === $appliedVacation->employee->manager_id && $appliedVacation->status === AppliedVacation::STATUS_PENDING);
    }
}
