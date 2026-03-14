<?php

namespace App\Policies;

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class AppliedVacationPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function apply(User $user, ?Employee $employee = null): bool
    {
        return $user->is_admin || $user->is_hr || $user->employee_id === $employee->id || $user->employee_id === $employee->manager_id;
    }
    
    public function applyForAny(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }
    
    public function applyLateForAny(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }


    /**
     * Determine whether the user can approve the applied vacation.
     */
    public function approve(User $user, AppliedVacation $appliedVacation): bool
    {
        return $appliedVacation->status !== AppliedVacation::STATUS_APPROVED &&
            ($user->is_admin || ($user->is_hr && !$user->permit_tibian));
        // return $appliedVacation->status !== AppliedVacation::STATUS_APPROVED &&
        //     ($user->is_admin || ($user->is_hr && !$user->permit_tibian) || ($user->employee_id === $appliedVacation->employee->manager_id && $appliedVacation->status === AppliedVacation::STATUS_PENDING));
    }

    /**
     * Determine whether the user can reject the applied vacation.
     */
    public function reject(User $user, AppliedVacation $appliedVacation): bool
    {
        return $appliedVacation->status !== AppliedVacation::STATUS_REJECTED && ($user->is_admin || ($user->is_hr && !$user->permit_tibian) || (($user->employee_id === $appliedVacation->employee->manager_id || $user->employee_id === $appliedVacation->employee->id) && $appliedVacation->status === AppliedVacation::STATUS_PENDING));
    }
}
