<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Users\AppLog;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('App Logs')]
#[Layout('components.layouts.app')]
class AppLogIndex extends Component
{
    use WithPagination;

    public $LogId;
    public $user;
    public $level;
    public $title;
    public $desc;
    public $time;
    public $numberOfPaginatorsRendered = 20;


    public $fromDate = '2023-01-01';
    public $toDate = '2023-06-01';

    protected $listeners = ['dateRangeSelected'];

    public function dateRangeSelected($data)
    {

        $this->fromDate = $data[0];
        $this->toDate = $data[1];
        $this->resetPage();
    }

    public function showLogInfo($id)
    {
        $this->LogId = $id;
        $log = AppLog::find($this->LogId);
        $this->user = $log->user?->username;
        $this->level = $log->level;
        $this->title = $log->title;
        $this->desc = $log->desc;
        $this->time = $log->created_at;
    }

    public function closeLogInfo()
    {
        $this->LogId = null;
        $this->user = null;
        $this->level = null;
        $this->title = null;
        $this->desc = null;
        $this->time = null;
    }

    public function render()
    {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate = Carbon::parse($this->toDate);

        $logs = AppLog::with('user')->orderBy('created_at', 'desc')->fromTo($fromDate, $toDate)->paginate($this->numberOfPaginatorsRendered);
        
        return view('livewire.settings.app-log-index', [
            'logs' => $logs,
        ]);
    }
}
