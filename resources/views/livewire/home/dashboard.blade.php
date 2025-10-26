<div class="container-fluid">
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard</h2>
        </div>
    </div>

    <div class="row">
        {{-- Latest Attendance Card --}}
        @if($employee && $latestAttendance->count() > 0)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Latest Attendance Records
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestAttendance as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->start_time }}</td>
                                    <td>{{ $attendance->end_time ?? 'N/A' }}</td>
                                    <td>{{ $attendance->hours }}</td>
                                    <td>
                                        @if($attendance->is_approved)
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($attendance->is_approved === false)
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Latest Applied Vacations Card --}}
        @if($employee && $latestAppliedVacations->count() > 0)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Latest Applied Vacations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Applied Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestAppliedVacations as $vacation)
                                <tr>
                                    <td>{{ $vacation->vacationBenefit->name ?? 'N/A' }}</td>
                                    <td>{{ $vacation->vacationDays->count() }}</td>
                                    <td>
                                        @if($vacation->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($vacation->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $vacation->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Admin/HR Only Cards --}}
        @if($user->is_admin || $user->is_hr)
        
        {{-- Upcoming Interviews Card --}}
        @if($upcomingInterviews->count() > 0)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Upcoming Interviews (Next 7 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Applicant</th>
                                    <th>Position</th>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingInterviews as $interview)
                                <tr>
                                    <td>{{ $interview->application->applicant->first_name }} {{ $interview->application->applicant->last_name }}</td>
                                    <td>{{ $interview->application->vacancy->title ?? 'N/A' }}</td>
                                    <td>{{ $interview->date->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($interview->type === 'in_person')
                                            <span class="badge bg-primary">In Person</span>
                                        @elseif($interview->type === 'online')
                                            <span class="badge bg-info">Online</span>
                                        @else
                                            <span class="badge bg-secondary">Phone</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($interview->status === 'scheduled')
                                            <span class="badge bg-success">Scheduled</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Latest Applicants Card --}}
        @if($latestApplicants->count() > 0)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Latest Applicants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Applied For</th>
                                    <th>Applied Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestApplicants as $applicant)
                                <tr>
                                    <td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td>
                                    <td>{{ $applicant->email }}</td>
                                    <td>{{ $applicant->phone }}</td>
                                    <td>
                                        @if($applicant->applications->count() > 0)
                                            {{ $applicant->applications->first()->vacancy->title ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $applicant->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Pending Vacation Requests Card --}}
        @if($pendingVacationRequests->count() > 0)
        <div class="col-lg-12 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pending Vacation Requests
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Vacation Type</th>
                                    <th>Days Requested</th>
                                    <th>Request Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingVacationRequests as $request)
                                <tr>
                                    <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                    <td>{{ $request->vacationBenefit->name ?? 'N/A' }}</td>
                                    <td>{{ $request->vacationDays->count() }}</td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success btn-sm" wire:click="approveVacation({{ $request->id }})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" wire:click="rejectVacation({{ $request->id }})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @endif

        {{-- Empty State Messages --}}
        @if($employee && $latestAttendance->count() === 0 && $latestAppliedVacations->count() === 0)
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Data Available</h5>
                    <p class="text-muted">You don't have any attendance records or vacation applications yet.</p>
                </div>
            </div>
        </div>
        @endif

        @if(($user->is_admin || $user->is_hr) && $upcomingInterviews->count() === 0 && $latestApplicants->count() === 0 && $pendingVacationRequests->count() === 0)
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-tachometer-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Administrative Data</h5>
                    <p class="text-muted">No upcoming interviews, new applicants, or pending vacation requests at the moment.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
