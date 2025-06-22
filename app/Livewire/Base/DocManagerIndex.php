<?php

namespace App\Livewire\Base;

use App\Models\Base\DocManager;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class DocManagerIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $setDocManagerSec = false;
    public $docManagerId;
    public $name;
    public $description;
    public $is_required = true;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable|string',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $docManagers = DocManager::search($this->search)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.base.doc-manager-index', [
            'docManagers' => $docManagers
        ]);
    }

    public function openEditDocManagerSec($id)
    {
        $docManager = DocManager::findOrFail($id);
        $this->docManagerId = $docManager->id;
        $this->name = $docManager->name;
        $this->description = $docManager->description;
        $this->is_required = $docManager->is_required;
        $this->is_active = $docManager->is_active;
        $this->setDocManagerSec = true;
    }

    public function closeSetDocManagerSec()
    {
        $this->resetForm();
        $this->setDocManagerSec = false;
    }

    public function resetForm()
    {
        $this->docManagerId = null;
        $this->name = '';
        $this->description = '';
        $this->is_required = true;
        $this->is_active = true;
    }

    public function editDocManager()
    {
        $this->validate();

        try {
            $docManager = DocManager::findOrFail($this->docManagerId);
            $docManager->updateDocManager(
                $this->name, 
                $this->description, 
                $this->is_required, 
                $this->is_active
            );
            $this->closeSetDocManagerSec();
            $this->alertSuccess('Document Manager updated successfully!');
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
}
