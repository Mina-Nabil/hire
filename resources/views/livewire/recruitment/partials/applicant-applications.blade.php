<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Applications</h3>
        <button type="button" class="btn btn-primary" wire:click="openNewApplicationModal">
            <i class="fas fa-plus mr-1"></i> Apply for Position
        </button>
    </div>

    @if($applicant->applications->count() > 0)
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applicant->applications as $application)
                        <tr>
                            <td>{{ $application->vacancy->position->title }}</td>
                            <td>{{ $application->vacancy->position->department->name }}</td>
                            <td>{{ $application->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $application->status_class }}">
                                    {{ $application->status }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="openNewInterviewModal({{ $application->id }})"
                                        @if(!in_array($application->status, ['New', 'Screening', 'Shortlisted'])) disabled @endif>
                                        <i class="fas fa-calendar-alt"></i> Schedule Interview
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-8">
                <div class="text-slate-400 mb-3">
                    <i class="fas fa-file-alt text-4xl"></i>
                </div>
                <h5 class="font-medium text-lg mb-1">No Applications Yet</h5>
                <p class="text-slate-500">This applicant hasn't applied for any positions yet.</p>
                <button type="button" class="btn btn-primary mt-4" wire:click="openNewApplicationModal">
                    Apply for Position
                </button>
            </div>
        </div>
    @endif

    <!-- New Application Modal -->
    <div class="modal @if($showNewApplicationModal) show @endif" tabindex="-1" role="dialog" style="display: @if($showNewApplicationModal) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Position</h5>
                    <button type="button" class="btn-close" wire:click="closeNewApplicationModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="vacancy_id" class="form-label">Select Position</label>
                        <select id="vacancy_id" class="form-select" wire:model="vacancyId">
                            <option value="">-- Select a position --</option>
                            @foreach($availableVacancies as $vacancy)
                                <option value="{{ $vacancy->id }}">
                                    {{ $vacancy->position->title }} ({{ $vacancy->position->department->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('vacancyId') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="application_notes" class="form-label">Notes (Optional)</label>
                        <textarea id="application_notes" class="form-control" wire:model="applicationNotes" rows="3" placeholder="Add any relevant notes about this application"></textarea>
                        @error('applicationNotes') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeNewApplicationModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="createApplication" wire:loading.attr="disabled">
                        <span wire:loading wire:target="createApplication" class="spinner-border spinner-border-sm mr-1"></span>
                        Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Interview Modal -->
    <div class="modal @if($showNewInterviewModal) show @endif" tabindex="-1" role="dialog" style="display: @if($showNewInterviewModal) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Interview</h5>
                    <button type="button" class="btn-close" wire:click="closeNewInterviewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="interview_date" class="form-label">Interview Date</label>
                        <input type="date" id="interview_date" class="form-control" wire:model="interviewDate">
                        @error('interviewDate') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="interview_time" class="form-label">Interview Time</label>
                        <input type="time" id="interview_time" class="form-control" wire:model="interviewTime">
                        @error('interviewTime') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="interview_type" class="form-label">Interview Type</label>
                        <select id="interview_type" class="form-select" wire:model="interviewType">
                            <option value="">-- Select type --</option>
                            <option value="In-Person">In-Person</option>
                            <option value="Phone">Phone</option>
                            <option value="Video">Video</option>
                        </select>
                        @error('interviewType') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="interview_location" class="form-label">Location/Link</label>
                        <input type="text" id="interview_location" class="form-control" wire:model="interviewLocation" placeholder="Office address or meeting link">
                        @error('interviewLocation') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="interview_notes" class="form-label">Notes (Optional)</label>
                        <textarea id="interview_notes" class="form-control" wire:model="interviewNotes" rows="3" placeholder="Additional information about the interview"></textarea>
                        @error('interviewNotes') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeNewInterviewModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="scheduleInterview" wire:loading.attr="disabled">
                        <span wire:loading wire:target="scheduleInterview" class="spinner-border spinner-border-sm mr-1"></span>
                        Schedule Interview
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 