<?php

namespace App\Livewire\Base;

use App\Models\Hierarchy\Location;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class LocationIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $showModal = false;
    public $locationId;
    public $name;

    protected $listeners = ['deleteLocation'];

    protected $rules = [
        'name' => 'required|min:3',
    ];

    public function render()
    {
        $locations = Location::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.base.location-index', [
            'locations' => $locations
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->locationId = null;
        $this->name = '';
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->locationId) {
                $location = Location::findOrFail($this->locationId);
                $location->editInfo($this->name);
                $this->alertSuccess('Location updated successfully!');
            } else {
                Location::createLocation($this->name);
                $this->alertSuccess('Location created successfully!');
            }
            $this->closeModal();
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        $this->locationId = $location->id;
        $this->name = $location->name;
        $this->showModal = true;
    }

    public function deleteLocation($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->deleteLocation();
            $this->alertSuccess('Location deleted successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
} 