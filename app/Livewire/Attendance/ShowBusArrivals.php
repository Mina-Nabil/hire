<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance\BusArrival;
use App\Models\Attendance\Bus;
use App\Traits\AlertFrontEnd;
use App\Exceptions\AppException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Bus Arrivals')]
#[Layout('components.layouts.app')]
class ShowBusArrivals extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $busFilter = '';
    public $showFilters = false;
    
    // Modal properties
    public $showCreateModal = false;
    public $selectedBusId = '';
    public $arrivalDateTime = '';
    
    public $isAdmin = false;
    public $isHr = false;

    protected $rules = [
        'selectedBusId' => 'required|exists:buses,id',
        'arrivalDateTime' => 'required|date',
    ];

    protected $messages = [
        'selectedBusId.required' => 'Please select a bus.',
        'selectedBusId.exists' => 'The selected bus is invalid.',
        'arrivalDateTime.required' => 'Please select arrival date and time.',
        'arrivalDateTime.date' => 'Please enter a valid date and time.',
    ];

    protected $listeners = ['deleteBusArrival'];

    public function mount()
    {
        // Check if the current user is admin or HR
        $user = Auth::user();
        $this->isAdmin = $user && $user->is_admin;
        $this->isHr = $user && $user->is_hr;
        
        // Set default arrival date time to current date and time
        $this->arrivalDateTime = now()->format('Y-m-d\TH:i');
    }
    
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'busFilter']);
    }

    public function openCreateModal()
    {
        if (!$this->isAdmin && !$this->isHr) {
            $this->alertError('You are not authorized to create bus arrivals.');
            return;
        }
        
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->resetValidation();
    }

    public function resetCreateForm()
    {
        $this->selectedBusId = '';
        $this->arrivalDateTime = now()->format('Y-m-d\TH:i');
    }

    public function createBusArrival()
    {
        $this->validate();

        try {
            // Parse the datetime input
            $dateTime = \Carbon\Carbon::parse($this->arrivalDateTime);
            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('H:i:s');

            // Create the bus arrival using the static method
            BusArrival::createBusArrival($this->selectedBusId, $date, $time);
            
            $this->alertSuccess('Bus arrival created successfully!');
            $this->closeCreateModal();
            
            // Reset pagination to show the new record
            $this->resetPage();
            
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to create bus arrival: ' . $e->getMessage());
        }
    }

    public function deleteBusArrival($arrivalId)
    {
        if (!$this->isAdmin && !$this->isHr) {
            $this->alertError('You are not authorized to delete bus arrivals.');
            return;
        }
        
        try {
            $arrival = BusArrival::findOrFail($arrivalId);
            
            // Check authorization using Gate
            if (!Gate::allows('delete', $arrival)) {
                $this->alertError('You are not authorized to delete this bus arrival.');
                return;
            }
            
            $arrival->delete();
            $this->alertSuccess('Bus arrival deleted successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to delete bus arrival: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = BusArrival::query()
            ->with('bus')
            ->when($this->search, function ($query) {
                $query->whereHas('bus', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate);
            })
            ->when($this->busFilter, function ($query) {
                $query->where('bus_id', $this->busFilter);
            });

        $busArrivals = $query->latest('date')->latest('time')->paginate(50);

        // Get all buses for the filter dropdown and create modal
        $buses = Bus::orderBy('name')->get();

        return view('livewire.attendance.show-bus-arrivals', [
            'busArrivals' => $busArrivals,
            'buses' => $buses,
            'isAdmin' => $this->isAdmin,
            'isHr' => $this->isHr
        ]);
    }
}
