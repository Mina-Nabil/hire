<?php

namespace App\Policies;

use App\Models\Attendance\Overtime;
use App\Models\Benefits\Payrolls\AppliedVacation;
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

    public function deleteLoan(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function deletePurchase(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function editExtraPayment(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function createExtraPayment(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function deleteExtraPayment(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function createPurchase(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr;
    }

    public function applyForVacation(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id || $user->employee_id === $employee->manager_id;
    }

    public function applyForVacationLate(User $user, Employee $employee): bool
    {
        return $user->is_admin || $user->is_hr || $user->id === $employee->user_id || $user->employee_id === $employee->manager_id;
    }

    public function approveVacation(User $user, Employee $employee, AppliedVacation $appliedVacation): bool
    {
        return $user->is_admin || $user->is_hr || ($user->employee_id === $employee->manager_id && $appliedVacation->status === AppliedVacation::STATUS_PENDING);
    }

    public function rejectVacation(User $user, Employee $employee, AppliedVacation $appliedVacation): bool
    {
        return $user->is_admin || $user->is_hr || ($user->employee_id === $employee->manager_id && $appliedVacation->status === AppliedVacation::STATUS_PENDING);
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
    public function setDocs(User $user, Employee $employee, $docType = null): bool
    {

        // allow admin and hr to upload docs
        if ($user->is_admin || $user->is_hr) {
            return true;
        }

        $employeeCanUpload = false;

        switch ($docType) {
            case 'armyServicePaper':
                $employeeCanUpload = !$employee->armyServicePaper()->exists();
                break;
            case 'birthCertificate':
                $employeeCanUpload = !$employee->birthCertificate()->exists();
                break;
            case 'idCard':
                $employeeCanUpload = !$employee->idCard()->exists();
                break;
            case 'driverLicense':
                $employeeCanUpload = !$employee->driverLicense()->exists();
                break;
            case 'employeeContract':
                $employeeCanUpload = !$employee->contracts()->exists();
                break;
            case 'employeeS1Doc':
                $employeeCanUpload = !$employee->employeeS1Doc()->exists();
                break;
            case 'employeeS2Doc':
                $employeeCanUpload = !$employee->employeeS2Doc()->exists();
                break;
            case 'employeeS6Doc':
                $employeeCanUpload = !$employee->employeeS6Doc()->exists();
                break;
            case 'policeRecord':
                $employeeCanUpload = !$employee->policeRecords()->exists();
                break;
            case 'hrLetter':
                // Employees can never upload HR letters
                $employeeCanUpload = false;
                break;
            case 'medicalRecord':
                $employeeCanUpload = !$employee->medicalRecord()->exists();
                break;
            case 'externalMedicalRecord':
                $employeeCanUpload = !$employee->externalMedicalRecord()->exists();
                break;
            case 'practiceCard':
                $employeeCanUpload = !$employee->practiceCard()->exists();
                break;
            case 'skillsQualification':
                $employeeCanUpload = !$employee->skillsQualifications()->exists();
                break;
            case 'syndicateCard':
                $employeeCanUpload = !$employee->syndicateCard()->exists();
                break;
            case 'workDeclaration':
                $employeeCanUpload = !$employee->workDeclarations()->exists();
                break;
            case 'labourDocument':
                $employeeCanUpload = !$employee->labourDocument()->exists();
                break;
            case 'collegeCertificate':
                $employeeCanUpload = !$employee->collegeCertificate()->exists();
                break;
            case 'socialPrint':
                $employeeCanUpload = !$employee->socialPrint()->exists();
                break;
            case 'otherDocument':
                $employeeCanUpload = !$employee->otherDocuments()->exists();
                break;
            case 'bankAccount':
                $employeeCanUpload = !$employee->bankAccounts()->exists();
                break;
            default:
                $employeeCanUpload = false;
        }

        // For employees, only allow if $employeeCanUpload is true
        return $employeeCanUpload;
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
        return $user->is_admin;
    }
}
