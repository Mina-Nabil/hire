    <!-- Schedule Interview Modal -->
    <x-modal wire:model="showNewInterviewModal">
        <x-slot name="title">Schedule Interview</x-slot>

        <div class="space-y-4">
            @if (isset($selectedApplication))
                <div class="alert alert-warning mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Scheduling interview for:</p>
                            <p>{{ $selectedApplication->vacancy->position->name }}
                                ({{ $selectedApplication->vacancy->position->department->name }})</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-text-input title="Interview Date" type="date" wire:model="interviewDate"
                        errorMessage="{{ $errors->first('interviewDate') }}" />

                    <x-text-input title="Interview Time" type="time" wire:model="interviewTime"
                        errorMessage="{{ $errors->first('interviewTime') }}" />
                </div>


                <x-select title="Interview Type" wire:model="interviewType"
                    errorMessage="{{ $errors->first('interviewType') }}">
                    <option value="">-- Select Type --</option>
                    @foreach ($interviewTypes as $type)
                        <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                    @endforeach
                </x-select>

                <x-text-input title="Location" wire:model="interviewLocation"
                    errorMessage="{{ $errors->first('interviewLocation') }}"
                    placeholder="Office location or meeting link..." />



                <x-textarea title="Notes" wire:model="interviewNotes" rows="3"
                    errorMessage="{{ $errors->first('interviewNotes') }}"
                    placeholder="Additional notes about this interview..." />
            @else
                <div class="alert alert-warning">
                    No application selected for interview scheduling.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeNewInterviewModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="scheduleInterview" loadingFunction="scheduleInterview">
                    Schedule Interview
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Interview Feedback Modal -->
    <x-modal wire:model="showFeedbackModal">
        <x-slot name="title">Interview Feedback</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Providing feedback for interview:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Conducted on: {{ $selectedInterview->date->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <x-select title="Interview Result" wire:model="interviewResult"
                    errorMessage="{{ $errors->first('interviewResult') }}">
                    <option value="">-- Select Result --</option>
                    <option value="Passed">Passed</option>
                    <option value="Failed">Failed</option>
                    <option value="On Hold">On Hold</option>
                </x-select>

                <div>
                    <x-input-label>Rating (1-10)</x-input-label>
                    <div class="flex items-center space-x-1">
                        @for ($i = 1; $i <= 10; $i++)
                            <button type="button"
                                class="p-1 rounded-full transition-colors {{ $rating >= $i ? 'text-yellow-400 hover:text-yellow-500' : 'text-gray-300 hover:text-gray-400' }}"
                                wire:click="$set('rating', {{ $i }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <x-textarea title="Strengths" wire:model="strengths" rows="3"
                    errorMessage="{{ $errors->first('strengths') }}"
                    placeholder="What were the candidate's key strengths?" />

                <x-textarea title="Weaknesses" wire:model="weaknesses" rows="3"
                    errorMessage="{{ $errors->first('weaknesses') }}" placeholder="What areas need improvement?" />

                <x-textarea title="Additional Feedback" wire:model="feedbackNotes" rows="3"
                    errorMessage="{{ $errors->first('feedbackNotes') }}"
                    placeholder="Any additional comments or observations..." />

                <x-select title="Next Steps" wire:model="nextStep" errorMessage="{{ $errors->first('nextStep') }}">
                    <option value="">-- Select Next Steps --</option>
                    <option value="Move to Next Round">Move to Next Round</option>
                    <option value="Make Offer">Make Offer</option>
                    <option value="Reject">Reject</option>
                    <option value="Keep on Hold">Keep on Hold</option>
                </x-select>

                <x-select title="Update Application Status" wire:model="newApplicationStatus"
                    errorMessage="{{ $errors->first('newApplicationStatus') }}">
                    <option value="">-- No Application Status Change --</option>
                    @foreach ($applicationStatuses as $status)
                        <option value="{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                    @endforeach
                </x-select>
            @else
                <div class="alert alert-warning">
                    No interview selected for feedback.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeFeedbackModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveInterviewFeedback" loadingFunction="saveInterviewFeedback">
                    Save Feedback
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Interview Feedback History Modal -->
    <x-modal wire:model="showFeedbacksHistoryModal">
        <x-slot name="title">Interview Feedback History</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview) && $selectedInterview->feedbacks->count() > 0)
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Feedback history for interview:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Conducted on: {{ $selectedInterview->date->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                @foreach ($selectedInterview->feedbacks as $feedback)
                    <div class="card mb-4">
                        <div class="card-header bg-slate-100 dark:bg-slate-700 p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-medium">{{ $feedback->user->name }}</span>
                                    <span
                                        class="text-sm text-slate-500 ml-2">{{ $feedback->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div>
                                    <span
                                        class="badge {{ $feedback->result === 'Passed' ? 'bg-success-200' : ($feedback->result === 'Failed' ? 'bg-danger-200' : 'bg-warning-200') }}">
                                        {{ $feedback->result }}
                                    </span>
                                    <span class="ml-2 text-yellow-400">
                                        {{ $feedback->rating }}/10
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <h5 class="font-medium text-sm text-slate-600 mb-1">Strengths:</h5>
                                <p class="whitespace-pre-line">{{ $feedback->strengths ?? 'Not provided' }}</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-medium text-sm text-slate-600 mb-1">Areas for Improvement:</h5>
                                <p class="whitespace-pre-line">{{ $feedback->weaknesses ?? 'Not provided' }}</p>
                            </div>
                            @if ($feedback->feedback)
                                <div>
                                    <h5 class="font-medium text-sm text-slate-600 mb-1">Additional Feedback:</h5>
                                    <p class="whitespace-pre-line">{{ $feedback->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @elseif(isset($selectedInterview) && $selectedInterview->feedbacks->count() === 0)
                <div class="alert alert-warning">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <div>
                            <p>No feedback has been provided for this interview yet.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <div>
                            <p>No interview selected.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeShowFeedbacksModal">Close</x-secondary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Set Interviewers Modal -->
    <x-modal wire:model="showSetInterviewersModal">
        <x-slot name="title">Assign Interviewers</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Assigning interviewers for:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label>Select Interviewers</x-input-label>
                    <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border rounded-md p-3">
                        @if (count($interviewers) > 0)
                            @foreach ($interviewers as $interviewer)
                                <div class="flex items-center">
                                    <input type="checkbox" id="interviewer-{{ $interviewer->id }}"
                                        wire:model="selectedInterviewers" value="{{ $interviewer->id }}"
                                        class="form-checkbox h-5 w-5 text-blue-600">
                                    <label for="interviewer-{{ $interviewer->id }}" class="ml-2 text-gray-700">
                                        {{ $interviewer->name }} ({{ $interviewer->type }})
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">No interviewers available.</p>
                        @endif
                    </div>
                    @error('selectedInterviewers')
                        <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <div class="alert alert-warning">
                    No interview selected.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeSetInterviewersModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="saveInterviewers" loadingFunction="saveInterviewers">
                    Assign Interviewers
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Reschedule Interview Modal -->
    <x-modal wire:model="showRescheduleModal">
        <x-slot name="title">Reschedule Interview</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Rescheduling interview for:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-text-input title="New Interview Date" type="date" wire:model="newInterviewDate"
                        errorMessage="{{ $errors->first('newInterviewDate') }}" />

                    <x-text-input title="New Interview Time" type="time" wire:model="newInterviewTime"
                        errorMessage="{{ $errors->first('newInterviewTime') }}" />
                </div>

                <x-select title="New Interview Type" wire:model="newInterviewType"
                    errorMessage="{{ $errors->first('newInterviewType') }}">
                    @foreach ($interviewTypes as $type)
                        <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                    @endforeach
                </x-select>

                <x-text-input title="New Location" wire:model="newInterviewLocation"
                    errorMessage="{{ $errors->first('newInterviewLocation') }}" placeholder="Office location..." />

                <x-text-input title="Zoom Link (if applicable)" wire:model="newInterviewZoomLink"
                    errorMessage="{{ $errors->first('newInterviewZoomLink') }}"
                    placeholder="https://zoom.us/j/..." />
            @else
                <div class="alert alert-warning">
                    No interview selected for rescheduling.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeRescheduleModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="rescheduleInterview" loadingFunction="rescheduleInterview">
                    Reschedule Interview
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Cancel Interview Modal -->
    <x-modal wire:model="showCancelModal">
        <x-slot name="title">Cancel Interview</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-danger mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <div>
                            <p class="font-medium">You are about to cancel this interview:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Scheduled for: {{ $selectedInterview->date->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <x-textarea title="Cancellation Reason" wire:model="cancelReason" rows="3"
                    errorMessage="{{ $errors->first('cancelReason') }}"
                    placeholder="Please provide a reason for cancellation..." />
            @else
                <div class="alert alert-warning">
                    No interview selected for cancellation.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeCancelModal">Go Back</x-secondary-button>
                <x-danger-button wire:click.prevent="cancelInterview" loadingFunction="cancelInterview">
                    Confirm Cancellation
                </x-danger-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Complete Interview Modal -->
    <x-modal wire:model="showCompleteModal">
        <x-slot name="title">Complete Interview</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">You are about to mark this interview as completed:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Scheduled for: {{ $selectedInterview->date->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-gray-600">
                    After marking this interview as completed, you will be prompted to provide feedback.
                </p>
            @else
                <div class="alert alert-warning">
                    No interview selected.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeCompleteModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="completeInterview" loadingFunction="completeInterview">
                    Mark as Completed
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Add Note Modal -->
    <x-modal wire:model="showAddNoteModal">
        <x-slot name="title">Add Interview Note</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Adding note for interview:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Scheduled for: {{ $selectedInterview->date->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                @foreach ($selectedInterview->notes as $note)
                    <div class="alert alert-light mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <div>
                                <p class="font-medium">{{ $note->title }} by {{ $note->user->name }}</p>
                                <p class="text-sm mt-1">{{ $note->note }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <x-text-input title="Note Title" wire:model="noteTitle"
                    errorMessage="{{ $errors->first('noteTitle') }}" placeholder="Brief title for this note..." />

                <x-textarea title="Note Content" wire:model="noteContent" rows="4"
                    errorMessage="{{ $errors->first('noteContent') }}" placeholder="Detailed note content..." />
            @else
                <div class="alert alert-warning">
                    No interview selected.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeAddNoteModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="addInterviewNote" loadingFunction="addInterviewNote">
                    Add Note
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Update Status Modal -->
    <x-modal wire:model="showUpdateStatusModal">
        <x-slot name="title">Update Interview Status</x-slot>

        <div class="space-y-4">
            @if (isset($selectedInterview))
                <div class="alert alert-info mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <div>
                            <p class="font-medium">Updating status for interview:</p>
                            <p>{{ $selectedInterview->application->vacancy->position->name }}
                                ({{ $selectedInterview->application->vacancy->position->department->name }})</p>
                            <p class="text-sm mt-1">Current status: <span
                                    class="badge {{ $selectedInterview->status_class }}">
                                    {{ $selectedInterview->status }}</span></p>
                        </div>
                    </div>
                </div>

                <x-select title="New Status" wire:model="newInterviewStatus"
                    errorMessage="{{ $errors->first('newInterviewStatus') }}">
                    <option value="">-- Select Status --</option>
                    @foreach ($interviewStatuses as $status)
                        <option value="{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                    @endforeach
                </x-select>
            @else
                <div class="alert alert-warning">
                    No interview selected.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeUpdateStatusModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="updateInterviewStatus" loadingFunction="updateInterviewStatus">
                    Update Status
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>
