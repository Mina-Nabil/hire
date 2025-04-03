<?php

namespace App\Livewire\Settings;

use App\Exceptions\AppException;
use App\Models\Base\Area;
use App\Models\Base\City;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class AreasIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    // Search
    public $search = '';

    // City section
    public $newCityModal = false;
    public $editCityModal = false;
    public $cityId;
    public $cityName;

    // Area section
    public $newAreaModal = false;
    public $editAreaModal = false;
    public $areaId;
    public $areaName;
    public $selectedCityId;

    // Confirmation modal
    public $deleteConfirmationModal = false;
    public $itemToDelete;
    public $itemTypeToDelete;

    //reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // City functions
    public function openNewCitySec()
    {
        $this->newCityModal = true;
        $this->resetCityFields();
    }

    public function closeNewCitySec()
    {
        $this->newCityModal = false;
    }

    public function addNewCity()
    {
        $this->validate([
            'cityName' => 'required|string|max:255',
        ]);

        try {
            City::newCity($this->cityName);
            $this->closeNewCitySec();
            $this->alertSuccess('City added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add city. Please try again.');
        }
    }

    public function openEditCitySec($id)
    {
        $city = City::find($id);
        $this->cityId = $city->id;
        $this->cityName = $city->name;
        $this->editCityModal = true;
    }

    public function closeEditCitySec()
    {
        $this->editCityModal = false;
    }

    public function updateCity()
    {
        $this->validate([
            'cityName' => 'required|string|max:255',
        ]);

        try {
            $city = City::find($this->cityId);
            $city->updateCity($this->cityName);
            $this->closeEditCitySec();
            $this->alertSuccess('City updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update city. Please try again.');
        }
    }

    public function confirmDeleteCity($id)
    {
        $this->itemToDelete = $id;
        $this->itemTypeToDelete = 'city';
        $this->deleteConfirmationModal = true;
    }

    public function deleteCity()
    {
        try {
            $city = City::find($this->itemToDelete);
            $city->deleteCity();
            $this->closeDeleteConfirmationModal();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete city. Please try again.');
        }
    }

    // Area functions
    public function openNewAreaSec()
    {
        $this->newAreaModal = true;
        $this->resetAreaFields();
    }

    public function closeNewAreaSec()
    {
        $this->newAreaModal = false;
    }

    public function addNewArea()
    {
        $this->validate([
            'areaName' => 'required|string|max:255',
            'selectedCityId' => 'required|exists:cities,id',
        ]);

        try {
            Area::newArea($this->areaName, $this->selectedCityId);
            $this->closeNewAreaSec();
            $this->alertSuccess('Area added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add area. Please try again.');
        }
    }

    public function openEditAreaSec($id)
    {
        $area = Area::find($id);
        $this->areaId = $area->id;
        $this->areaName = $area->name;
        $this->selectedCityId = $area->city_id;
        $this->editAreaModal = true;
    }

    public function closeEditAreaSec()
    {
        $this->editAreaModal = false;
    }

    public function updateArea()
    {
        $this->validate([
            'areaName' => 'required|string|max:255',
            'selectedCityId' => 'required|exists:cities,id',
        ]);

        try {
            $area = Area::find($this->areaId);

            $area->updateArea($this->areaName, $this->selectedCityId);
            $this->closeEditAreaSec();
            $this->alertSuccess('Area updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update area. Please try again.');
        }
    }

    public function confirmDeleteArea($id)
    {
        $this->itemToDelete = $id;
        $this->itemTypeToDelete = 'area';
        $this->deleteConfirmationModal = true;
    }

    public function deleteArea()
    {
        try {
            $area = Area::find($this->itemToDelete);
            try {
                $area->deleteArea();
                $this->closeDeleteConfirmationModal();
                $this->alertSuccess('Area deleted successfully!');
            } catch (AppException $e) {
                $this->alertError($e->getMessage());
            } catch (Exception $e) {
                report($e);
                $this->alertError('Failed to delete area. Please try again.');
            }
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete area. Please try again.');
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
        if ($this->itemTypeToDelete === 'city') {
            $this->deleteCity();
        } else if ($this->itemTypeToDelete === 'area') {
            $this->deleteArea();
        }
    }

    private function resetCityFields()
    {
        $this->cityId = null;
        $this->cityName = '';
    }

    private function resetAreaFields()
    {
        $this->areaId = null;
        $this->areaName = '';
        $this->selectedCityId = '';
    }

    public function render()
    {
        $cities = City::search($this->search)
            ->withCount('areas')
        ->orderBy('name')->get();
        $areas = Area::search($this->search)
            ->with('city')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.areas-index', [
            'cities' => $cities,
            'areas' => $areas,
        ])->layout('components.layouts.app', [
            'title' => 'Areas',
            'areasIndex' => 'active'
        ]);
    }
}
