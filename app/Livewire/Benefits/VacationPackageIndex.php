<?php

namespace App\Livewire\Benefits;

use App\Exceptions\AppException;
use App\Models\Benefits\Configurations\VacationPackage;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;

class VacationPackageIndex extends Component
{
    use AlertFrontEnd;
    public $packages;
    public $editingPackageId;
    public $isEditing;
    public $name;
    public $desc;
    public $vacationDetails = [];
    public $vacationDetailTypes;
    public $showAddModal = false;

    public $listeners = ['applyPackageToAllActiveEmployees'];


    public function loadPackages()
    {
        $this->packages = VacationPackage::withCount(['vacationDetails'])
            ->get();
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->reset(['name', 'desc', 'vacationDetails']);
    }

    public function showCreateModal()
    {
        $this->reset(['name', 'desc', 'vacationDetails', 'editingPackageId', 'isEditing']);
        $this->showAddModal = true;
    }


    public function showEditModal($packageId)
    {
        $package = VacationPackage::with(['vacationDetails'])->findOrFail($packageId);
        $this->editingPackageId = $packageId;
        $this->isEditing = true;
        $this->name = $package->name;
        $this->desc = $package->desc;

        // Load vacation details
        $this->vacationDetails = $package->vacationDetails->map(function ($detail) {
            return [
                'name' => $detail->name,
                'type' => $detail->type,
                'inc_rate_min' => $detail->inc_rate_min,
                'inc_rate_max' => $detail->inc_rate_max,
                'max_balance_min' => $detail->max_balance_min,
                'max_balance_max' => $detail->max_balance_max,
                'hour_price_min' => $detail->hour_price_min,
                'hour_price_max' => $detail->hour_price_max,
                'apply_deadline' => $detail->apply_deadline,
            ];
        })->toArray();

        $this->showAddModal = true;
    }

    public function addVacationDetail()
    {
        $this->vacationDetails[] = [
            'name' => '',
            'type' => '',
            'inc_rate_min' => '',
            'inc_rate_max' => '',
            'max_balance_min' => '',
            'max_balance_max' => '',
            'hour_price_min' => '',
            'hour_price_max' => '',
            'apply_deadline' => '',
        ];
    }

    public function removeVacationDetail($index)
    {
        unset($this->vacationDetails[$index]);
        $this->vacationDetails = array_values($this->vacationDetails);
    }


    public function savePackage()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'vacationDetails' => 'array',
            'vacationDetails.*.name' => 'required|string|max:255',
            'vacationDetails.*.type' => 'required|string|in:' . implode(',', VacationDetail::TYPE_LIST),
            'vacationDetails.*.inc_rate_min' => 'required|numeric|min:0',
            'vacationDetails.*.inc_rate_max' => 'required|numeric|min:0',
            'vacationDetails.*.max_balance_min' => 'required|numeric|min:0',
            'vacationDetails.*.max_balance_max' => 'required|numeric|min:0',
            'vacationDetails.*.hour_price_min' => 'required|numeric|min:0',
            'vacationDetails.*.hour_price_max' => 'required|numeric|min:0',
            'vacationDetails.*.apply_deadline' => 'nullable|integer|min:0',
        ], [
            'vacationDetails.*.name.required' => 'Name#:position is required',
            'vacationDetails.*.type.required' => 'Type#:position is required',
            'vacationDetails.*.inc_rate_min.required' => 'Increase rate minimum#:position is required',
            'vacationDetails.*.inc_rate_max.required' => 'Increase rate maximum#:position is required',
            'vacationDetails.*.max_balance_min.required' => 'Max balance minimum#:position is required',
            'vacationDetails.*.max_balance_max.required' => 'Max balance maximum#:position is required',
            'vacationDetails.*.hour_price_min.required' => 'Hour price minimum#:position is required',
            'vacationDetails.*.hour_price_max.required' => 'Hour price maximum#:position is required',
            'vacationDetails.*.apply_deadline.integer' => 'Apply deadline#:position must be a number',
            'vacationDetails.*.apply_deadline.min' => 'Apply deadline#:position must be 0 or greater',
        ]);

        try {
            if ($this->isEditing) {
                $package = VacationPackage::findOrFail($this->editingPackageId);
                $package->editPackage(
                    $this->name,
                    $this->desc,
                    $this->vacationDetails,
                );
                $this->alertSuccess('Package updated successfully!');
            } else {
                VacationPackage::createVacationPackage(
                    $this->name,
                    $this->desc,
                    $this->vacationDetails,
                );
                $this->alertSuccess('Package created successfully!');
            }

            $this->showAddModal = false;
            $this->loadPackages();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            $this->alertError('Failed to save package: ' . $e->getMessage());
        }
    }

    public function deletePackage($packageId)
    {
        try {
            $package = VacationPackage::findOrFail($packageId);
            $package->delete();
            $this->loadPackages();
            $this->alertSuccess('Package deleted successfully!');
        } catch (Exception $e) {
            $this->alertError('Failed to delete package: ' . $e->getMessage());
        }
    }

    public function applyPackageToAllActiveEmployees($packageId)
    {
        try {
            $package = VacationPackage::findOrFail($packageId);
            $result = Employee::applyVacationPackageToAllActiveEmployees($package);
            
            $message = "Successfully applied vacation package to {$result['success_count']} out of {$result['total_count']} active employees.";
            
            if (!empty($result['errors'])) {
                $errorCount = count($result['errors']);
                $message .= " {$errorCount} employee(s) had errors.";
                
                if ($errorCount <= 5) {
                    $message .= " Errors: " . implode(', ', array_column($result['errors'], 'error'));
                }
            }
            
            if ($result['success_count'] > 0) {
                $this->alertSuccess($message);
            } else {
                $this->alertError('Failed to apply package to any employees. ' . ($result['errors'][0]['error'] ?? 'Unknown error'));
            }
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            $this->alertError('Failed to apply package to employees: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->vacationDetailTypes = VacationDetail::TYPE_LIST;
        $this->loadPackages();
    }

    public function render()
    {
        return view('livewire.benefits.vacation-package-index');
    }
}
