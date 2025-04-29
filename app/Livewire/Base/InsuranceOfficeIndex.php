<?php

namespace App\Livewire\Base;

use App\Models\Base\InsuranceOffice;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class InsuranceOfficeIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $setInsuranceOfficeSec = false;
    public $insuranceOfficeId;
    public $name;
    public $arabic_name;

    protected $listeners = ['deleteInsuranceOffice'];

    protected $rules = [
        'name' => 'required|min:3',
        'arabic_name' => 'required|min:3',
    ];

    public function render()
    {
        $insuranceOffices = InsuranceOffice::search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.base.insurance-office-index', [
            'insuranceOffices' => $insuranceOffices
        ])->layout('components.layouts.app', [
            'title' => 'Insurance Offices',
            'insuranceOfficesIndex' => 'active'
        ]);
    }

    public function openNewInsuranceOfficeSec()
    {
        $this->resetForm();
        $this->setInsuranceOfficeSec = true;
    }

    public function closeSetInsuranceOfficeSec()
    {
        $this->resetForm();
        $this->setInsuranceOfficeSec = false;
    }

    public function resetForm()
    {
        $this->insuranceOfficeId = null;
        $this->name = '';
        $this->arabic_name = '';
    }

    public function addNewInsuranceOffice()
    {
        $this->validate();

        try {
            InsuranceOffice::createInsuranceOffice($this->name, $this->arabic_name);
            $this->closeSetInsuranceOfficeSec();
            $this->alertSuccess('Insurance Office created successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function updateThisInsuranceOffice($id)
    {
        $insuranceOffice = InsuranceOffice::findOrFail($id);
        $this->insuranceOfficeId = $insuranceOffice->id;
        $this->name = $insuranceOffice->name;
        $this->arabic_name = $insuranceOffice->arabic_name;
        $this->setInsuranceOfficeSec = $id;
    }

    public function editInsuranceOffice()
    {
        $this->validate();

        try {
            $insuranceOffice = InsuranceOffice::findOrFail($this->insuranceOfficeId);
            $insuranceOffice->editInsuranceOffice($this->name, $this->arabic_name);
            $this->closeSetInsuranceOfficeSec();
            $this->alertSuccess('Insurance Office updated successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function deleteInsuranceOffice($id)
    {
        try {
            $insuranceOffice = InsuranceOffice::findOrFail($id);
            $insuranceOffice->deleteInsuranceOffice();
            $this->alertSuccess('Insurance Office deleted successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
} 