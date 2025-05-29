<?php

namespace App\Livewire\Base;

use App\Models\Base\Bank;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class BankIndex extends Component
{
    use WithPagination , AlertFrontEnd;

    public $search = '';
    public $setBankSec = false;
    public $bankId;
    public $name;
    public $arabic_name;

    protected $listeners = ['deleteBank'];

    protected $rules = [
        'name' => 'required|min:3',
        'arabic_name' => 'required|min:3',
    ];

    public function render()
    {
        $banks = Bank::search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.base.bank-index', [
            'banks' => $banks
        ]);
    }

    public function openNewBankSec()
    {
        $this->resetForm();
        $this->setBankSec = true;
    }

    public function closeSetBankSec()
    {
        $this->resetForm();
        $this->setBankSec = false;
    }

    public function resetForm()
    {
        $this->bankId = null;
        $this->name = '';
        $this->arabic_name = '';
    }

    public function addNewBank()
    {
        $this->validate();

        try {
            Bank::createBank($this->name, $this->arabic_name);
            $this->closeSetBankSec();
            $this->alertSuccess('Bank created successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function updateThisBank($id)
    {
        $bank = Bank::findOrFail($id);
        $this->bankId = $bank->id;
        $this->name = $bank->name;
        $this->arabic_name = $bank->arabic_name;
        $this->setBankSec = $id;
    }

    public function editBank()
    {
        $this->validate();

        try {
            $bank = Bank::findOrFail($this->bankId);
            $bank->editBank($this->name, $this->arabic_name);
            $this->closeSetBankSec();
            $this->alertSuccess('Bank updated successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function deleteBank($id)
    {
        try {
            $bank = Bank::findOrFail($id);
            $bank->deleteBank();
            $this->alertSuccess('Bank deleted successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
} 