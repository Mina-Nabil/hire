<?php

namespace App\Livewire\Home;

use App\Models\Attendance\Attendance;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Personel\Employee;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $user;
    public $employee;
    public $latestAttendance;
    public $latestAppliedVacations;
    public $upcomingInterviews;
    public $latestApplicants;
    public $pendingVacationRequests;

    public function mount()
    {
        $this->user = Auth::user();
        $this->employee = Employee::where('user_id', $this->user->id)->first();
        
        // Initialize collections
        $this->latestAttendance = collect();
        $this->latestAppliedVacations = collect();
        $this->upcomingInterviews = collect();
        $this->latestApplicants = collect();
        $this->pendingVacationRequests = collect();
        
        $this->loadUserData();
        
        if ($this->user->is_admin || $this->user->is_hr) {
            $this->loadAdminHrData();
        }
    }

    private function loadUserData()
    {
        // Load latest 10 attendance records for current user
        if ($this->employee) {
            $this->latestAttendance = Attendance::where('employee_id', $this->employee->id)
                ->orderBy('date', 'desc')
                ->limit(10)
                ->with(['employee'])
                ->get();

            // Load latest 10 applied vacations for current user
            $this->latestAppliedVacations = AppliedVacation::where('employee_id', $this->employee->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->with(['vacationBenefit', 'vacationDays'])
                ->get();
        }
    }

    private function loadAdminHrData()
    {
        // Load upcoming interviews (next 7 days)
        $this->upcomingInterviews = Interview::where('date', '>=', now())
            ->where('date', '<=', now()->addDays(7))
            ->whereIn('status', [Interview::STATUS_PENDING, Interview::STATUS_SCHEDULED])
            ->orderBy('date', 'asc')
            ->limit(10)
            ->with(['application.applicant', 'application.vacancy'])
            ->get();

        // Load latest 10 applicants
        $this->latestApplicants = Applicant::orderBy('created_at', 'desc')
            ->limit(10)
            ->with(['applications.vacancy.position', 'applications'])
            ->get();

        // Load pending vacation requests
        $this->pendingVacationRequests = AppliedVacation::where('status', AppliedVacation::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with(['employee', 'vacationBenefit', 'vacationDays'])
            ->get();
    }

    public function approveVacation($vacationId)
    {
        try {
            $vacation = AppliedVacation::findOrFail($vacationId);
            $vacation->approve();
            
            // Refresh the data
            $this->loadAdminHrData();
            
            session()->flash('message', 'Vacation request approved successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve vacation request: ' . $e->getMessage());
        }
    }

    public function rejectVacation($vacationId)
    {
        try {
            $vacation = AppliedVacation::findOrFail($vacationId);
            $vacation->reject('Rejected from dashboard');
            
            // Refresh the data
            $this->loadAdminHrData();
            
            session()->flash('message', 'Vacation request rejected successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject vacation request: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.home.dashboard');
    }
}
