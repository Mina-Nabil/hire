<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Interviews</h3>
    </div>

    @if($interviews->count() > 0)
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($interviews as $interview)
                        <tr>
                            <td>{{ $interview->application->vacancy->position->title }}</td>
                            <td>{{ $interview->interview_date->format('d M Y') }} at {{ $interview->interview_time }}</td>
                            <td>{{ $interview->type }}</td>
                            <td>{{ $interview->location }}</td>
                            <td>
                                <span class="badge {{ $interview->status_class }}">
                                    {{ $interview->status }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="openFeedbackModal({{ $interview->id }})"
                                        @if(!in_array($interview->status, ['Scheduled', 'In Progress'])) disabled @endif>
                                        <i class="fas fa-comment"></i> Feedback
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
                    <i class="fas fa-calendar-alt text-4xl"></i>
                </div>
                <h5 class="font-medium text-lg mb-1">No Interviews Scheduled</h5>
                <p class="text-slate-500">This applicant doesn't have any interviews scheduled yet.</p>
            </div>
        </div>
    @endif

    <!-- Interview Feedback Modal -->
    <div class="modal @if($showFeedbackModal) show @endif" tabindex="-1" role="dialog" style="display: @if($showFeedbackModal) block @else none @endif;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Interview Feedback</h5>
                    <button type="button" class="btn-close" wire:click="closeFeedbackModal"></button>
                </div>
                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-slate-500">Position</p>
                            <p class="font-medium">{{ $selectedInterview ? $selectedInterview->application->vacancy->position->title : '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Department</p>
                            <p class="font-medium">{{ $selectedInterview ? $selectedInterview->application->vacancy->position->department->name : '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Interview Date</p>
                            <p class="font-medium">{{ $selectedInterview ? $selectedInterview->interview_date->format('d M Y') : '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Interview Type</p>
                            <p class="font-medium">{{ $selectedInterview ? $selectedInterview->type : '' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-medium mb-2">Feedback & Evaluation</h6>
                        <div class="mb-3">
                            <label for="interview_result" class="form-label">Result</label>
                            <select id="interview_result" class="form-select" wire:model="interviewResult">
                                <option value="">-- Select result --</option>
                                <option value="Passed">Passed</option>
                                <option value="Failed">Failed</option>
                                <option value="On Hold">On Hold</option>
                            </select>
                            @error('interviewResult') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Overall Rating (1-5)</label>
                            <select id="rating" class="form-select" wire:model="rating">
                                <option value="">-- Select rating --</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Below Average</option>
                                <option value="3">3 - Average</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                            @error('rating') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="strengths" class="form-label">Strengths</label>
                            <textarea id="strengths" class="form-control" wire:model="strengths" rows="2" placeholder="Candidate's key strengths"></textarea>
                            @error('strengths') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="weaknesses" class="form-label">Areas for Improvement</label>
                            <textarea id="weaknesses" class="form-control" wire:model="weaknesses" rows="2" placeholder="Areas where candidate could improve"></textarea>
                            @error('weaknesses') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="feedback_notes" class="form-label">Additional Comments</label>
                            <textarea id="feedback_notes" class="form-control" wire:model="feedbackNotes" rows="3" placeholder="Any additional comments or observations"></textarea>
                            @error('feedbackNotes') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="next_step" class="form-label">Recommended Next Step</label>
                        <select id="next_step" class="form-select" wire:model="nextStep">
                            <option value="">-- Select next step --</option>
                            <option value="Schedule Another Interview">Schedule Another Interview</option>
                            <option value="Move to Offer Stage">Move to Offer Stage</option>
                            <option value="Reject Candidate">Reject Candidate</option>
                            <option value="Keep on Hold">Keep on Hold</option>
                        </select>
                        @error('nextStep') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFeedbackModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveInterviewFeedback" wire:loading.attr="disabled">
                        <span wire:loading wire:target="saveInterviewFeedback" class="spinner-border spinner-border-sm mr-1"></span>
                        Save Feedback
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 