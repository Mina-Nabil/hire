<?php

namespace App\Policies;

use App\Models\Attendance\Overtime;
use App\Models\Personel\Employee;
use App\Models\Personel\Docs\EmployeeHrLetterRequest;
use App\Models\Users\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{

    public function updateEmployeeBenefits(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    public function addOvertime(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    public function createLoan(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function createExtraPayment(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function createPurchase(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function applyForVacation(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    public function approveVacation(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function rejectVacation(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }


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
     * Determine whether the user can view the dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can view the missing document report.
     */
    public function viewMissingDocReport(User $user): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function createHrLetterRequest(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id;
    }

    public function updateHrLetterRequest(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function updateOvertime(User $user, Employee $employee): bool
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
     * Determine whether the user can set employee docs.
     */
    public function setDocs(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    /**
     * Determine whether the user can delete employee docs.
     */
    public function deleteDocs(User $user, Employee $employee): bool
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
