<?php

namespace App\Livewire\Benefits;

use App\Exceptions\AppException;
use Livewire\Component;
use App\Models\Benefits\Configurations\BenefitPackage;
use Illuminate\Support\Collection;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Vacations\VacationDetail as VacationDetailModel;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\Log;

class PackageIndex extends Component
{
    use AlertFrontEnd;
    public $packages;
    public $showAddModal = false;
    public $name = '';
    public $desc = '';
    public $packageDetails = [];
    public $vacationDetails = [];
    public $packageDetailTypes = [];
    public $vacationDetailTypes = [];
    public $packageDetailReceivers = [];
    public $editingPackageId = null;
    public $isEditing = false;
    public function mount()
    {
        $this->packageDetailTypes = BaseBenefit::TYPE_LIST;
        $this->vacationDetailTypes = VacationDetailModel::TYPE_LIST;
        $this->packageDetailReceivers = PackageDetail::RECEIVER_LIST;
        $this->loadPackages();
    }

    public function loadPackages()
    {
        $this->packages = BenefitPackage::withCount(['packageDetails', 'vacationDetails'])
            ->get();
    }

    public function showCreateModal()
    {
        $this->reset(['name', 'desc', 'packageDetails', 'vacationDetails', 'editingPackageId', 'isEditing']);
        $this->showAddModal = true;
    }

    public function showEditModal($packageId)
    {
        $package = BenefitPackage::with(['packageDetails', 'vacationDetails'])->findOrFail($packageId);
        $this->editingPackageId = $packageId;
        $this->isEditing = true;
        $this->name = $package->name;
        $this->desc = $package->desc;

        // Load package details
        $this->packageDetails = $package->packageDetails->map(function ($detail) {
            return [
                'name' => $detail->name,
                'type' => $detail->type,
                'amount_min' => $detail->amount_min,
                'amount_max' => $detail->amount_max,
                'receiver' => $detail->receiver,
                'is_net' => $detail->is_net ? 'net' : '',
                'is_gross' => $detail->is_gross ? 'gross' : '',
                'is_grand_gross' => $detail->is_grand_gross ? 'grand_gross' : '',
                'is_hidden' => $detail->is_hidden,
            ];
        })->toArray();

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
            'is_net' => 'net',
            'is_gross' => '',
            'is_grand_gross' => '',
            'is_hidden' => false,
        ];
    }

    public function removePackageDetail($index)
    {
        unset($this->packageDetails[$index]);
        $this->packageDetails = array_values($this->packageDetails);
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
            'packageDetails' => 'array',
            'packageDetails.*.receiver' => 'required|string|in:' . implode(',', PackageDetail::RECEIVER_LIST),
            'packageDetails.*.name' => 'required|string|max:255',
            'packageDetails.*.type' => 'required|string|in:' . implode(',', BaseBenefit::TYPE_LIST),
            'packageDetails.*.amount_min' => 'required|numeric|min:0',
            'packageDetails.*.amount_max' => 'required|numeric|min:0',
            'vacationDetails' => 'array',
            'vacationDetails.*.name' => 'required|string|max:255',
            'vacationDetails.*.type' => 'required|string|in:' . implode(',', VacationDetailModel::TYPE_LIST),
            'vacationDetails.*.inc_rate_min' => 'required|numeric|min:0',
            'vacationDetails.*.inc_rate_max' => 'required|numeric|min:0',
            'vacationDetails.*.max_balance_min' => 'required|numeric|min:0',
            'vacationDetails.*.max_balance_max' => 'required|numeric|min:0',
            'vacationDetails.*.hour_price_min' => 'required|numeric|min:0',
            'vacationDetails.*.hour_price_max' => 'required|numeric|min:0',
        ], [
            'packageDetails.*.receiver.required' => 'Receiver#:position is required',
            'packageDetails.*.receiver.in' => 'Invalid receiver in row#:position',
            'packageDetails.*.name.required' => 'Name#:position is required',
            'packageDetails.*.type.required' => 'Type#:position is required',
            'packageDetails.*.amount_min.required' => 'Amount minimum#:position is required',
            'packageDetails.*.amount_max.required' => 'Amount maximum#:position is required',
            'vacationDetails.*.name.required' => 'Name#:position is required',
            'vacationDetails.*.type.required' => 'Type#:position is required',
            'vacationDetails.*.inc_rate_min.required' => 'Increase rate minimum#:position is required',
            'vacationDetails.*.inc_rate_max.required' => 'Increase rate maximum#:position is required',
            'vacationDetails.*.max_balance_min.required' => 'Max balance minimum#:position is required',
            'vacationDetails.*.max_balance_max.required' => 'Max balance maximum#:position is required',
            'vacationDetails.*.hour_price_min.required' => 'Hour price minimum#:position is required',
            'vacationDetails.*.hour_price_max.required' => 'Hour price maximum#:position is required',
        ]);

        foreach ($this->packageDetails as $index => $detail) {
            if($detail['is_net'] == 'net') {
                $this->packageDetails[$index]['is_net'] = 1;
                $this->packageDetails[$index]['is_gross'] = 0;
                $this->packageDetails[$index]['is_grand_gross'] = 0;
            }
            if($detail['is_gross'] == 'gross') {
                $this->packageDetails[$index]['is_net'] = 0;
                $this->packageDetails[$index]['is_gross'] = 1;
                $this->packageDetails[$index]['is_grand_gross'] = 0;
            }
            if($detail['is_grand_gross'] == 'grand_gross') {
                $this->packageDetails[$index]['is_net'] = 0;
                $this->packageDetails[$index]['is_gross'] = 0;
                $this->packageDetails[$index]['is_grand_gross'] = 1;
            }
            
            
            
        }

        try {
            if ($this->isEditing) {
                $package = BenefitPackage::findOrFail($this->editingPackageId);
                $package->editPackage(
                    $this->name,
                    $this->desc,
                    $this->packageDetails,
                    $this->vacationDetails
                );
                $this->alertSuccess('Package updated successfully!');
            } else {
                BenefitPackage::createPackage(
                    $this->name,
                    $this->desc,
                    $this->packageDetails,
                    $this->vacationDetails
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
            $package = BenefitPackage::findOrFail($packageId);
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
                'description' => 'Benefits Packages found on the system, please use the filters to find the package you are looking for',
                'packageIndex' => 'active',
            ]);
    }
}
