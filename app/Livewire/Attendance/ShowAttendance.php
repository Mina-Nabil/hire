<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance\Attendance;
use App\Models\Personel\Employee;
use App\Traits\AlertFrontEnd;
use App\Exceptions\AppException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Attendance')]
class ShowAttendance extends Component
{
    use WithPagination, AlertFrontEnd;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $isApproved = '';
    public $showFilters = false;
    
    // Modal for extra hours editing
    public $showExtraHoursModal = false;
    public $editingAttendanceId = null;
    public $editExtraHours = null;
    public $employeeName = '';
    public $attendanceDate = '';
    public $attendanceHours = '';
    public $isManager = false;
    public $isHr = false;
    public $isAdmin = false;

    // Modal for attendance times editing
    public $showEditTimesModal = false;
    public $editingTimesAttendanceId = null;
    public $editStartTime = '';
    public $editEndTime = '';
    public $editTimesEmployeeName = '';
    public $editTimesAttendanceDate = '';
    public $editTimesCurrentHours = '';

    public function mount()
    {
        // Check if the current user is a manager or HR
        $user = Auth::user();
        $this->isHr = $user && $user->is_hr;
        $this->isAdmin = $user && $user->is_admin;
        $employee = Employee::where('user_id', $user->id)->first();
        $this->isManager = $employee && $employee->is_manager;
    }
    
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'startDate', 'endDate', 'isApproved']);
    }

    public function approveExtraHours($attendanceId)
    {
        if (!$this->isHr) {
            $this->alertError('Only HR can approve extra hours.');
            return;
        }
        
        try {
            $attendance = Attendance::findOrFail($attendanceId);
            $attendance->approveExtraHours();
            $this->alertSuccess('Extra hours approved successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to approve extra hours: ' . $e->getMessage());
        }
    }

    public function rejectExtraHours($attendanceId)
    {
        if (!$this->isHr) {
            $this->alertError('Only HR can reject extra hours.');
            return;
        }
        
        try {
            $attendance = Attendance::findOrFail($attendanceId);
            $attendance->rejectExtraHours();
            $this->alertSuccess('Extra hours rejected successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to reject extra hours: ' . $e->getMessage());
        }
    }
    
    public function approveAttendance($attendanceId)
    {

        try {
            $attendance = Attendance::findOrFail($attendanceId);
            $attendance->approveAttendance();
            $this->alertSuccess('Attendance record approved successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to approve attendance: ' . $e->getMessage());
        }
    }
    
    public function rejectAttendance($attendanceId)
    {
        if (!$this->isManager) {
            $this->alertError('Only managers can reject attendance records.');
            return;
        }
        
        try {
            $attendance = Attendance::findOrFail($attendanceId);
            
            // Verify the manager is authorized to reject this employee's attendance
            $userEmployee = Employee::where('user_id', Auth::id())->first();
            $employeeConfig = $attendance->employee->benefitConfiguration;
            
            if (!$userEmployee || !$employeeConfig || $employeeConfig->manager_id !== $userEmployee->id) {
                $this->alertError('You are not authorized to reject this attendance record.');
                return;
            }
            
            $attendance->rejectAttendance();
            $this->alertSuccess('Attendance record rejected successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to reject attendance: ' . $e->getMessage());
        }
    }
    
    // Open the modal to edit extra hours
    public function openEditExtraHours($attendanceId)
    {
        $attendance = Attendance::with('employee')->findOrFail($attendanceId);
        $this->editingAttendanceId = $attendanceId;
        $this->editExtraHours = $attendance->extra_hours;
        $this->employeeName = $attendance->employee ? $attendance->employee->name : 'N/A';
        $this->attendanceDate = $attendance->date;
        $this->attendanceHours = $attendance->hours;
        $this->showExtraHoursModal = true;
    }
    
    // Close the modal and reset fields
    public function closeExtraHoursModal()
    {
        $this->showExtraHoursModal = false;
        $this->reset(['editingAttendanceId', 'editExtraHours', 'employeeName', 'attendanceDate', 'attendanceHours']);
    }
    
    // Save extra hours from the modal
    public function saveExtraHours()
    {
        // Validate
        $this->validate([
            'editExtraHours' => 'nullable|numeric',
        ]);
        
        try {
            $attendance = Attendance::findOrFail($this->editingAttendanceId);
            
            // Update the extra hours
            $attendance->extra_hours = $this->editExtraHours;
            // Reset approval status since the extra hours have changed
            $attendance->is_extra_hours_approved = null;
            $attendance->save();
            
            $this->alertSuccess('Extra hours updated successfully! The HR team will review and approve these hours.');
            $this->closeExtraHoursModal();
        } catch (\Exception $e) {
            $this->alertError('Failed to update extra hours: ' . $e->getMessage());
        }
    }

    // Open the modal to edit attendance times
    public function openEditTimes($attendanceId)
    {
        $attendance = Attendance::with('employee')->findOrFail($attendanceId);
        $this->editingTimesAttendanceId = $attendanceId;
        $this->editStartTime = Carbon::parse($attendance->start_time)->format('H:i');
        $this->editEndTime = Carbon::parse($attendance->end_time)->format('H:i');
        $this->editTimesEmployeeName = $attendance->employee ? $attendance->employee->name : 'N/A';
        $this->editTimesAttendanceDate = $attendance->date;
        $this->editTimesCurrentHours = $attendance->hours;
        $this->showEditTimesModal = true;
    }
    
    // Close the modal and reset fields
    public function closeEditTimesModal()
    {
        $this->showEditTimesModal = false;
        $this->reset(['editingTimesAttendanceId', 'editStartTime', 'editEndTime', 'editTimesEmployeeName', 'editTimesAttendanceDate', 'editTimesCurrentHours']);
    }
    
    // Save attendance times from the modal
    public function saveAttendanceTimes()
    {
        // Validate
        $this->validate([
            'editStartTime' => 'required|date_format:H:i',
            'editEndTime' => 'nullable|date_format:H:i|after:editStartTime',
        ], [
            'editStartTime.required' => 'Start time is required.',
            'editStartTime.date_format' => 'Start time must be in HH:MM format.',
            'editEndTime.date_format' => 'End time must be in HH:MM format.',
            'editEndTime.after' => 'End time must be after start time.',
        ]);
        
        try {
            $attendance = Attendance::findOrFail($this->editingTimesAttendanceId);
            
            // Update the attendance times using the model method
            $attendance->editAttendanceTimes($this->editStartTime, $this->editEndTime);
            
            $this->alertSuccess('Attendance times updated successfully!');
            $this->closeEditTimesModal();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to update attendance times: ' . $e->getMessage());
        }
    }

    public function deleteAttendance($attendanceId)
    {
        try {
            $user = Auth::user();
            if (!$user->can('delete', Attendance::class)) {
                $this->alertError('You are not authorized to delete this attendance record.');
                return;
            }
            $attendance = Attendance::findOrFail($attendanceId);
            
            $attendance->delete();
            
            $this->alertSuccess('Attendance record deleted successfully!');
            $this->closeEditTimesModal();
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (\Exception $e) {
            $this->alertError('Failed to delete attendance: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Attendance::query()
            ->with('employee')
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->where('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('date', '<=', $this->endDate);
            })
            ->when($this->isApproved !== '', function ($query) {
                if ($this->isApproved === 'approved') {
                    $query->where('is_approved', true);
                } elseif ($this->isApproved === 'rejected') {
                    $query->where('is_approved', false);
                } elseif ($this->isApproved === 'pending') {
                    $query->whereNull('is_approved');
                }
            });

        $attendances = $query->paginate(50);

        // Determine which layout to use based on user role
        $user = Auth::user();
        $layout = 'components.layouts.app'; // Default layout
        
        if ($user) {
            if ($user->is_admin || $user->is_hr) {
                $layout = 'components.layouts.app'; // Admin/HR layout
            } else {
                $layout = 'components.layouts.employee'; // Employee layout
            }
        }

        $loggedInUser = Auth::user();
        if($loggedInUser->is_admin || $loggedInUser->is_hr){
            $layout = 'components.layouts.app';
        }else{
            $layout = 'components.layouts.employee';
        }

        return view('livewire.attendance.show-attendance', [
            'attendances' => $attendances,
            'isManager' => $this->isManager,
            'isHr' => $this->isHr,
            'isAdmin' => $this->isAdmin,
        ])->layout($layout);
    }
}
