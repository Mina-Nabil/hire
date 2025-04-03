<?php

namespace App\Livewire\Organization;

use App\Exceptions\AppException;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class PositionIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    // Search
    public $search = '';

    // Department section
    public $newDepartmentModal = false;
    public $editDepartmentModal = false;
    public $departmentId;
    public $departmentName;
    public $departmentPrefixCode;
    public $departmentDescription;

    // Position section
    public $newPositionModal = false;
    public $editPositionModal = false;
    public $positionId;
    public $positionName;
    public $positionArabicName;
    public $selectedDepartmentId;
    public $jobDescription;
    public $arabicJobDescription;
    public $jobRequirements;
    public $arabicJobRequirements;
    public $jobQualifications;
    public $arabicJobQualifications;
    public $jobBenefits;
    public $arabicJobBenefits;
    public $parentId;
    public $code;
    public $sapCode;

    // Confirmation modal
    public $deleteConfirmationModal = false;
    public $itemToDelete;
    public $itemTypeToDelete;

    //reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Department functions
    public function openNewDepartmentSec()
    {
        $this->newDepartmentModal = true;
        $this->resetDepartmentFields();
    }

    public function closeNewDepartmentSec()
    {
        $this->newDepartmentModal = false;
    }

    public function addNewDepartment()
    {
        $this->validate([
            'departmentName' => 'required|string|max:255',
            'departmentPrefixCode' => 'required|string|max:10',
            'departmentDescription' => 'nullable|string',
        ]);

        try {
            Department::createDepartment($this->departmentName, $this->departmentPrefixCode, $this->departmentDescription);
            $this->closeNewDepartmentSec();
            $this->alertSuccess('Department added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add department. Please try again.');
        }
    }

    public function openEditDepartmentSec($id)
    {
        $department = Department::find($id);
        $this->departmentId = $department->id;
        $this->departmentName = $department->name;
        $this->departmentPrefixCode = $department->prefix_code;
        $this->departmentDescription = $department->desc;
        $this->editDepartmentModal = true;
    }

    public function closeEditDepartmentSec()
    {
        $this->editDepartmentModal = false;
    }

    public function updateDepartment()
    {
        $this->validate([
            'departmentName' => 'required|string|max:255',
            'departmentPrefixCode' => 'required|string|max:10',
            'departmentDescription' => 'nullable|string',
        ]);

        try {
            $department = Department::find($this->departmentId);
            $department->editInfo($this->departmentName, $this->departmentPrefixCode, $this->departmentDescription);
            $this->closeEditDepartmentSec();
            $this->alertSuccess('Department updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update department. Please try again.');
        }
    }

    public function confirmDeleteDepartment($id)
    {
        $this->itemToDelete = $id;
        $this->itemTypeToDelete = 'department';
        $this->deleteConfirmationModal = true;
    }

    public function deleteDepartment()
    {
        try {
            /** @var Department $department */
            $department = Department::find($this->itemToDelete);
            $department->deleteDepartment();
            $this->closeDeleteConfirmationModal();
            $this->alertSuccess('Department deleted successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete department. Please try again.');
        }
    }

    // Position functions
    public function openNewPositionSec()
    {
        $this->newPositionModal = true;
        $this->resetPositionFields();
    }

    public function closeNewPositionSec()
    {
        $this->newPositionModal = false;
    }

    public function addNewPosition()
    {
        $this->validate([
            'positionName' => 'required|string|max:255',
            'positionArabicName' => 'required|string|max:255',
            'selectedDepartmentId' => 'required|exists:departments,id',
            'jobDescription' => 'nullable|string',
            'arabicJobDescription' => 'nullable|string',
            'jobRequirements' => 'nullable|string',
            'arabicJobRequirements' => 'nullable|string',
            'jobQualifications' => 'nullable|string',
            'arabicJobQualifications' => 'nullable|string',
            'jobBenefits' => 'nullable|string',
            'arabicJobBenefits' => 'nullable|string',
            'parentId' => 'nullable|exists:positions,id',
        ]);

        try {
            $position = Position::createPosition(
                $this->selectedDepartmentId,
                $this->positionName,
                $this->parentId,
                $this->positionArabicName,
                $this->jobDescription,
                $this->arabicJobDescription,
                $this->jobRequirements,
                $this->arabicJobRequirements,
                $this->jobQualifications,
                $this->arabicJobQualifications,
                $this->jobBenefits,
                $this->arabicJobBenefits,
                $this->code,
                $this->sapCode
            );

            $this->closeNewPositionSec();
            $this->alertSuccess('Position added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add position. Please try again.');
        }
    }

    public function openEditPositionSec($id)
    {
        $position = Position::find($id);
        $this->positionId = $position->id;
        $this->positionName = $position->name;
        $this->positionArabicName = $position->arabic_name;
        $this->selectedDepartmentId = $position->department_id;
        $this->jobDescription = $position->job_description;
        $this->arabicJobDescription = $position->arabic_job_description;
        $this->jobRequirements = $position->job_requirements;
        $this->arabicJobRequirements = $position->arabic_job_requirements;
        $this->jobQualifications = $position->job_qualifications;
        $this->arabicJobQualifications = $position->arabic_job_qualifications;
        $this->jobBenefits = $position->job_benefits;
        $this->arabicJobBenefits = $position->arabic_job_benefits;
        $this->parentId = $position->parent_id;
        $this->code = $position->code;
        $this->sapCode = $position->sap_code;
        $this->editPositionModal = true;
    }

    public function closeEditPositionSec()
    {
        $this->editPositionModal = false;
    }

    public function updatePosition()
    {
        $this->validate([
            'positionName' => 'required|string|max:255',
            'positionArabicName' => 'required|string|max:255',
            'selectedDepartmentId' => 'required|exists:departments,id',
            'jobDescription' => 'nullable|string',
            'arabicJobDescription' => 'nullable|string',
            'jobRequirements' => 'nullable|string',
            'arabicJobRequirements' => 'nullable|string',
            'jobQualifications' => 'nullable|string',
            'arabicJobQualifications' => 'nullable|string',
            'jobBenefits' => 'nullable|string',
            'arabicJobBenefits' => 'nullable|string',
            'parentId' => 'nullable|exists:positions,id',
        ]);

        try {
            $position = Position::find($this->positionId);

            // Validate that position is not set as its own parent
            if ($this->parentId == $this->positionId) {
                $this->alertError('A position cannot be its own parent.');
                return;
            }

            $position->editInfo(
                $this->selectedDepartmentId,
                $this->positionName,
                $this->parentId,
                $this->positionArabicName,
                $this->jobDescription,
                $this->arabicJobDescription,
                $this->jobRequirements,
                $this->arabicJobRequirements,
                $this->jobQualifications,
                $this->arabicJobQualifications,
                $this->jobBenefits,
                $this->arabicJobBenefits,
                $this->code,
                $this->sapCode
            );

            $this->closeEditPositionSec();
            $this->alertSuccess('Position updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update position. Please try again.');
        }
    }

    public function confirmDeletePosition($id)
    {
        $this->itemToDelete = $id;
        $this->itemTypeToDelete = 'position';
        $this->deleteConfirmationModal = true;
    }

    public function deletePosition()
    {
        try {
            $position = Position::find($this->itemToDelete);
            $position->deletePosition();
            $this->alertSuccess('Position deleted successfully!');
            $this->closeDeleteConfirmationModal();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete position. Please try again.');
        }
    }

    // Helper functions
    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemToDelete = null;
        $this->itemTypeToDelete = null;
    }

    public function confirmDelete()
    {
        if ($this->itemTypeToDelete === 'department') {
            $this->deleteDepartment();
        } else if ($this->itemTypeToDelete === 'position') {
            $this->deletePosition();
        }
    }

    private function resetDepartmentFields()
    {
        $this->departmentId = null;
        $this->departmentName = '';
        $this->departmentPrefixCode = '';
        $this->departmentDescription = '';
    }

    private function resetPositionFields()
    {
        $this->positionId = null;
        $this->positionName = '';
        $this->positionArabicName = '';
        $this->selectedDepartmentId = '';
        $this->jobDescription = '';
        $this->arabicJobDescription = '';
        $this->jobRequirements = '';
        $this->arabicJobRequirements = '';
        $this->jobQualifications = '';
        $this->arabicJobQualifications = '';
        $this->jobBenefits = '';
        $this->arabicJobBenefits = '';
        $this->parentId = null;
    }

    public function render()
    {
        $departments = Department::when($this->search, function ($query, $v) {
            return $query->search($v);
        })
            ->withCount('positions')
            ->orderBy('name')
            ->get();

        $positions = Position::when($this->search, function ($query, $v) {
            return $query->search($v);
        })
            ->with(['department', 'parent', 'children', 'employee'])
            ->orderBy('name')
            ->paginate(10);

        $allPositions = Position::orderBy('name')->get();

        return view('livewire.organization.position-index', [
            'departments' => $departments,
            'positions' => $positions,
            'allPositions' => $allPositions
        ])->layout('components.layouts.app', [
            'title' => 'Positions',
            'positionsIndex' => 'active'
        ]);
    }
}
