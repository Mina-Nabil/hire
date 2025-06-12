<?php

namespace App\Livewire\Base;

use App\Models\Attendance\Bus;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class BusIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $setBusSec = false;
    public $busId;
    public $name;

    protected $listeners = ['deleteBus'];

    protected $rules = [
        'name' => 'required|min:3',
    ];

    public function render()
    {
        $buses = Bus::search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.base.bus-index', [
            'buses' => $buses
        ]);
    }

    public function openNewBusSec()
    {
        $this->resetForm();
        $this->setBusSec = true;
    }

    public function closeSetBusSec()
    {
        $this->resetForm();
        $this->setBusSec = false;
    }

    public function resetForm()
    {
        $this->busId = null;
        $this->name = '';
    }

    public function addNewBus()
    {
        $this->validate();

        try {
            Bus::createBus($this->name);
            $this->closeSetBusSec();
            $this->alertSuccess('Bus created successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function updateThisBus($id)
    {
        $bus = Bus::findOrFail($id);
        $this->busId = $bus->id;
        $this->name = $bus->name;
        $this->setBusSec = $id;
    }

    public function editBus()
    {
        $this->validate();

        try {
            $bus = Bus::findOrFail($this->busId);
            $bus->editBus($this->name);
            $this->closeSetBusSec();
            $this->alertSuccess('Bus updated successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function deleteBus($id)
    {
        try {
            $bus = Bus::findOrFail($id);
            $bus->deleteBus();
            $this->alertSuccess('Bus deleted successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
}
