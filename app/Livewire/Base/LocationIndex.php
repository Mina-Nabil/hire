<?php

namespace App\Livewire\Base;

use App\Models\Hierarchy\Location;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class LocationIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $showModal = false;
    public $locationId;
    
    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('nullable|numeric|between:-90,90')]
    public $latitude = '';

    #[Validate('nullable|numeric|between:-180,180')]
    public $longitude = '';

    // HR User assignment properties
    public $hrUsersModal = false;
    public $selectedLocation = null;
    public $selectedUsers = [];
    public $availableHrUsers = [];

    protected $listeners = ['deleteLocation'];

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
        $this->resetValidation();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->reset(['locationId', 'name', 'latitude', 'longitude']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
        
        try {
            if ($this->locationId) {
                $location = Location::findOrFail($this->locationId);
                $location->editInfo($this->name, $this->latitude ?? null, $this->longitude ?? null);
                $this->alertSuccess('Location updated successfully!');
            } else {
                Location::createLocation($this->name, $this->latitude ?? null, $this->longitude ?? null);
                $this->alertSuccess('Location created successfully!');
            }
            
            $this->resetValidation();
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
        $this->latitude = $location->latitude;
        $this->longitude = $location->longitude;
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

    // Open HR users assignment modal
    public function openHrUsersModal($id)
    {
        $this->selectedLocation = Location::findOrFail($id);
        
        // Get all available HR users
        $this->availableHrUsers = User::hr()->active()->get();
        
        // Get location's currently assigned HR users
        $this->selectedUsers = $this->selectedLocation->assignedHrUsers()->pluck('users.id')->toArray();
        
        $this->hrUsersModal = true;
    }
    
    // Close HR users assignment modal
    public function closeHrUsersModal()
    {
        $this->hrUsersModal = false;
        $this->reset(['selectedLocation', 'selectedUsers', 'availableHrUsers']);
    }
    
    // Save HR user assignments
    public function saveHrUserAssignments()
    {
        try {
            if (!$this->selectedLocation) {
                throw new \Exception('Location not found');
            }
            
            $this->selectedLocation->setAssignedHrUsers($this->selectedUsers);
            $this->closeHrUsersModal();
            $this->alertSuccess('HR users assigned successfully');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
} 