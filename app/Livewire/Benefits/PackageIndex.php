<?php

namespace App\Livewire\Benefits;

use App\Exceptions\AppException;
use Livewire\Component;
use App\Models\Benefits\Configurations\SalaryGrade;
use Illuminate\Support\Collection;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Traits\AlertFrontEnd;
use Exception;

class PackageIndex extends Component
{
    use AlertFrontEnd;
    public $packages;
    public $showAddModal = false;
    public $name = '';
    public $desc = '';
    public $grossMin = '';
    public $grossMax = '';
    public $packageDetails = [];
    public $packageDetailTypes = [];
    public $packageDetailReceivers = [];
    public $editingPackageId = null;
    public $isEditing = false;

    public function mount()
    {
        $this->packageDetailTypes = BaseBenefit::TYPE_LIST;
        $this->packageDetailReceivers = PackageDetail::RECEIVER_LIST;
        $this->loadPackages();
    }

    public function loadPackages()
    {
        $this->packages = SalaryGrade::withCount(['packageDetails'])
            ->get();
    }

    public function showCreateModal()
    {
        $this->reset(['name', 'desc', 'packageDetails', 'editingPackageId', 'isEditing']);
        $this->showAddModal = true;
    }

    public function showEditModal($packageId)
    {
        $package = SalaryGrade::with(['packageDetails'])->findOrFail($packageId);
        $this->editingPackageId = $packageId;
        $this->isEditing = true;
        $this->name = $package->name;
        $this->desc = $package->desc;
        $this->grossMin = $package->gross_min;
        $this->grossMax = $package->gross_max;

        // Load package details
        $this->packageDetails = $package->packageDetails->map(function ($detail) {
            return [
                'name' => $detail->name,
                'type' => $detail->type,
                'amount_min' => $detail->amount_min,
                'amount_max' => $detail->amount_max,
                'receiver' => $detail->receiver,
                'is_hidden' => $detail->is_hidden,
            ];
        })->toArray();

        $this->showAddModal = true;
    }

    public function addPackageDetail()
    {
        $this->packageDetails[] = [
            'name' => '',
            'type' => '',
            'amount_min' => '',
            'amount_max' => '',
            'receiver' => '',
            'is_hidden' => false,
        ];
    }

    public function removePackageDetail($index)
    {
        unset($this->packageDetails[$index]);
        $this->packageDetails = array_values($this->packageDetails);
    }


    public function savePackage()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'grossMin' => 'required|numeric|min:0',
            'grossMax' => 'required|numeric|min:0|gt:grossMin',
            'packageDetails' => 'array',
            'packageDetails.*.receiver' => 'required|string|in:' . implode(',', PackageDetail::RECEIVER_LIST),
            'packageDetails.*.name' => 'required|string|max:255',
            'packageDetails.*.type' => 'required|string|in:' . implode(',', BaseBenefit::TYPE_LIST),
            'packageDetails.*.amount_min' => 'required|numeric|min:0',
            'packageDetails.*.amount_max' => 'required|numeric|min:0',
        ], [
            'packageDetails.*.receiver.required' => 'Receiver#:position is required',
            'packageDetails.*.receiver.in' => 'Invalid receiver in row#:position',
            'grossMin.required' => 'Gross minimum is required',
            'grossMax.required' => 'Gross maximum is required',
            'grossMax.gt' => 'Gross maximum must be greater than gross minimum',
            'packageDetails.*.name.required' => 'Name#:position is required',
            'packageDetails.*.type.required' => 'Type#:position is required',
            'packageDetails.*.amount_min.required' => 'Amount minimum#:position is required',
            'packageDetails.*.amount_max.required' => 'Amount maximum#:position is required',
        ]);

        try {
            if ($this->isEditing) {
                $package = SalaryGrade::findOrFail($this->editingPackageId);
                $package->editPackage(
                    $this->name,
                    $this->grossMin,
                    $this->grossMax,
                    $this->packageDetails,
                    $this->desc,
                );
                $this->alertSuccess('Package updated successfully!');
            } else {
                SalaryGrade::createSalaryGrade(
                    $this->name,
                    $this->grossMin,
                    $this->grossMax,
                    $this->packageDetails,
                    $this->desc,
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
            $package = SalaryGrade::findOrFail($packageId);
            $package->delete();
            $this->loadPackages();
            $this->alertSuccess('Package deleted successfully!');
        } catch (Exception $e) {
            $this->alertError('Failed to delete package: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.benefits.package-index')
            ->layout('components.layouts.app', [
                'title' => 'Benefits Packages',
                'packageIndex' => 'active',
            ]);
    }
}
