<?php

namespace App\Livewire\Recruitment;

use App\Models\Recruitment\Applicants\Channel;
use App\Traits\AlertFrontEnd;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class ChannelIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    // =============== Search Section ===============
    public $search = '';
    public function updatingSearch()
    {
        $this->resetPage();
    }
    // ===============================================

    // =============== Create Channel Section ===============
    public $showCreateModal = false;
    public $name = '';
    public $is_hidden = false;
    protected $rules = [
        'name' => 'required|string|max:255',
        'is_hidden' => 'boolean'
    ];

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->name = '';
        $this->is_hidden = false;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'is_hidden']);
    }

    public function createChannel()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'is_hidden' => 'boolean'
        ]);

        try {
            $channel = new Channel([
                'name' => $this->name,
                'is_hidden' => $this->is_hidden
            ]);
            $channel->save();

            if ($channel) {
                $this->closeCreateModal();
                $this->alert('success', 'Channel created successfully');
            }
        } catch (Exception $e) {
            $this->alert('error', 'Failed to create channel');
        }
    }
    // ===============================================

    // =============== Edit Channel Section ===============
    public $showEditModal = false;
    public $selectedChannel = null;
    public $editName = '';
    public $editIsHidden = false;

    public function openEditModal(Channel $channel)
    {
        $this->selectedChannel = $channel;
        $this->editName = $channel->name;
        $this->editIsHidden = $channel->is_hidden;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['selectedChannel', 'editName', 'editIsHidden']);
    }

    public function updateChannel()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editIsHidden' => 'boolean'
        ]);

        try {
            $this->selectedChannel->name = $this->editName;
            $this->selectedChannel->is_hidden = $this->editIsHidden;
            if ($this->selectedChannel->save()) {
                $this->closeEditModal();
                $this->alert('success', 'Channel updated successfully');
            }
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update channel');
        }
    }
    // ===============================================

    // =============== Delete Channel Section ===============
    public $showDeleteModal = false;

    public function openDeleteModal(Channel $channel)
    {
        $this->selectedChannel = $channel;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['selectedChannel']);
    }

    public function deleteChannel()
    {
        try {
            if ($this->selectedChannel->deleteChannel()) {
                $this->closeDeleteModal();
                $this->alert('success', 'Channel deleted successfully');
            }
        } catch (Exception $e) {
            $this->alert('error', 'Failed to delete channel');
        }
    }
    // ===============================================

    // =============== Render Section ===============
    public function render()
    {
        $channels = Channel::where('name', 'like', '%' . $this->search . '%')
            ->paginate(20);

        return view('livewire.recruitment.channel-index', [
            'channels' => $channels
        ]);
    }
    // ===============================================
} 