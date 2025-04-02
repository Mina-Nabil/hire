<?php

namespace App\Livewire\Settings;

use App\Models\Users\AppLog;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LogsIndex extends Component
{
    use WithPagination;

    public $logs;

    public $LogId;
    public $user;
    public $level;
    public $title;
    public $desc;
    public $time;

    public $fromDate = '2023-01-01';
    public $toDate = '2023-06-01';
    protected $listeners = ['dateRangeSelected'];

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

    public function dateRangeSelected($startDate, $endDate)
    {

        $this->fromDate = $startDate;
        $this->toDate = $endDate;

        $this->resetPage();
    }


    public function render()
    {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate = Carbon::parse($this->toDate);

        $this->logs = AppLog::with('user')->orderBy('created_at', 'desc')->fromTo($fromDate, $toDate)->paginate(20);

        return view('livewire.settings.logs-index');
    }
}
