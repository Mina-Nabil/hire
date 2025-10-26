<div>
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h2 class="font-bold text-2xl mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Latest Attendance Card --}}
        @if($employee && $latestAttendance->count() > 0)
        <div class="card">
            <div class="card-header py-3">
                <h5 class="font-medium m-0">
                    <i class="fas fa-clock mr-2"></i>
                    Latest Attendance Records
                </h5>
            </div>
            <div class="card-body mt-2">
                <div class="divide-y">
                    @foreach($latestAttendance as $attendance)
                    <div class="p-4">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <p class="font-medium">{{ $attendance->date->format('M d, Y') }}</p>
                                    @if($attendance->is_approved)
                                        <span class="badge bg-success text-white">Approved</span>
                                    @elseif($attendance->is_approved === false)
                                        <span class="badge bg-danger text-white">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-white">Pending</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-500">
                                    <span>{{ $attendance->start_time }}</span>
                                    @if($attendance->end_time)
                                        <span> - {{ $attendance->end_time }}</span>
                                    @endif
                                    <span> · {{ $attendance->hours }} hrs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Latest Applied Vacations Card --}}
        @if($employee && $latestAppliedVacations->count() > 0)
        <div class="card">
            <div class="card-header py-3">
                <h5 class="font-medium m-0">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Latest Applied Vacations
                </h5>
            </div>
            <div class="card-body mt-2">
                <div class="divide-y">
                    @foreach($latestAppliedVacations as $vacation)
                    <div class="p-4">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <p class="font-medium">{{ $vacation->vacationBenefit->name ?? 'N/A' }}</p>
                                    @if($vacation->status === 'approved')
                                        <span class="badge bg-success text-white">Approved</span>
                                    @elseif($vacation->status === 'rejected')
                                        <span class="badge bg-danger text-white">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-white">Pending</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-500">
                                    <span>{{ $vacation->vacationDays->count() }} days</span>
                                    <span> · Applied {{ $vacation->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Admin/HR Only Cards --}}
    @if($user->is_admin || $user->is_hr)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Upcoming Interviews Card --}}
        @if($upcomingInterviews->count() > 0)
        <div class="card">
            <div class="card-header py-3">
                <h5 class="font-medium m-0">
                    <i class="fas fa-user-tie mr-2"></i>
                    Upcoming Interviews (Next 7 Days)
                </h5>
            </div>
            <div class="card-body mt-2">
                <div class="divide-y">
                    @foreach($upcomingInterviews as $interview)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="font-medium">{{ $interview->application->applicant->first_name }} {{ $interview->application->applicant->last_name }}</p>
                                    @if($interview->status === 'scheduled')
                                        <span class="badge bg-success text-white">Scheduled</span>
                                    @else
                                        <span class="badge bg-warning text-white">Pending</span>
                                    @endif
                                </div>
                                <p class="text-slate-600 text-sm mb-1">{{ $interview->application->vacancy->title ?? 'N/A' }}</p>
                                <p class="text-slate-500 text-sm">
                                    {{ $interview->date->format('M d, Y H:i') }}
                                    @if($interview->type === 'in_person')
                                        <span class="badge bg-primary text-white ml-2">In Person</span>
                                    @elseif($interview->type === 'online')
                                        <span class="badge bg-info text-white ml-2">Online</span>
                                    @else
                                        <span class="badge bg-secondary text-white ml-2">Phone</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Latest Applicants Card --}}
        @if($latestApplicants->count() > 0)
        <div class="card">
            <div class="card-header py-3">
                <h5 class="font-medium m-0">
                    <i class="fas fa-users mr-2"></i>
                    Latest Applicants
                </h5>
            </div>
            <div class="card-body mt-2">
                <div class="divide-y">
                    @foreach($latestApplicants as $applicant)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium mb-1">{{ $applicant->first_name }} {{ $applicant->last_name }}</p>
                                <p class="text-slate-500 text-sm mb-1">{{ $applicant->email }}</p>
                                @if($applicant->phone)
                                    <p class="text-slate-500 text-sm">{{ $applicant->phone }}</p>
                                @endif
                                @if($applicant->applications->count() > 0)
                                    <p class="text-slate-600 text-sm mt-1">
                                        Applied for: {{ $applicant->applications->first()->vacancy->title ?? 'N/A' }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-slate-500 text-sm">
                                {{ $applicant->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Pending Vacation Requests Card --}}
    @if($pendingVacationRequests->count() > 0)
    <div class="card mb-6">
        <div class="card-header py-3">
            <h5 class="font-medium m-0">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Pending Vacation Requests
            </h5>
        </div>
        <div class="card-body mt-2">
            <div class="divide-y">
                @foreach($pendingVacationRequests as $request)
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <p class="font-medium">{{ $request->employee->name ?? 'N/A' }}</p>
                                <span class="badge bg-warning text-white">Pending</span>
                            </div>
                            <p class="text-slate-600 text-sm mb-1">{{ $request->vacationBenefit->name ?? 'N/A' }}</p>
                            <p class="text-slate-500 text-sm">
                                {{ $request->vacationDays->count() }} days requested
                                · Applied {{ $request->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="btn btn-sm btn-success" wire:click="approveVacation({{ $request->id }})">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger" wire:click="rejectVacation({{ $request->id }})">
                                <i class="fas fa-times mr-1"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Empty State Messages --}}
    @if($employee && $latestAttendance->count() === 0 && $latestAppliedVacations->count() === 0)
    <div class="card">
        <div class="card-body text-center py-8">
            <i class="fas fa-chart-line fa-3x text-slate-400 mb-3"></i>
            <h5 class="font-medium text-slate-600 mb-2">No Data Available</h5>
            <p class="text-slate-500">You don't have any attendance records or vacation applications yet.</p>
        </div>
    </div>
    @endif

    @if(($user->is_admin || $user->is_hr) && $upcomingInterviews->count() === 0 && $latestApplicants->count() === 0 && $pendingVacationRequests->count() === 0)
    <div class="card">
        <div class="card-body text-center py-8">
            <i class="fas fa-tachometer-alt fa-3x text-slate-400 mb-3"></i>
            <h5 class="font-medium text-slate-600 mb-2">No Administrative Data</h5>
            <p class="text-slate-500">No upcoming interviews, new applicants, or pending vacation requests at the moment.</p>
        </div>
    </div>
    @endif
</div>
