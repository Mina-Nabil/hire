<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Applications</h3>
        <button type="button" class="btn btn-primary" wire:click="openNewApplicationModal">
            <i class="fas fa-plus mr-1"></i> Apply for Position
        </button>
    </div>
    <div class="card">
        @if ($applicant->applications->count() > 0)
            <div class="card-body px-6 pb-6">
                <div class=" -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
                                <thead
                                    class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class=" table-th">Position</th>
                                        <th scope="col" class=" table-th">Department</th>
                                        <th scope="col" class=" table-th">Applied Date</th>
                                        <th scope="col" class=" table-th">Status</th>
                                        <th scope="col" class=" table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($applicant->applications as $application)
                                        <tr>
                                            <td class="table-td">{{ $application->vacancy->position->name }}</td>
                                            <td class="table-td">{{ $application->vacancy->position->department->name }}</td>
                                            <td class="table-td">{{ $application->created_at->format('d M Y') }}</td>
                                            <td class="table-td">
                                                <span class="badge {{ $application->status_class }}">
                                                    {{ $application->status }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <div class="flex space-x-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        wire:click="openNewInterviewModal({{ $application->id }})">
                                                        <i class="fas fa-calendar-alt"></i> Schedule Interview
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
        @else
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
        @endif
    </div>

    <!-- Apply for Position Modal -->
    <x-modal wire:model="showNewApplicationModal">
        <x-slot name="title">Apply for Position</x-slot>

        <div class="space-y-4">
            @if (count($availableVacancies) > 0)
                <div>
                    <x-input-label>Select Position</x-input-label>
                    <x-select wire:model="vacancyId" errorMessage="{{ $errors->first('vacancyId') }}">
                        <option value="">-- Select Position --</option>
                        @foreach ($availableVacancies as $vacancy)
                            <option value="{{ $vacancy->id }}">
                                {{ $vacancy->position->sap_code }} - {{ $vacancy->position->name }}
                                ({{ $vacancy->position->department->name }})
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-input-label>Application Notes</x-input-label>
                    <x-textarea wire:model="applicationNotes" placeholder="Additional notes about this application"
                        rows="3" errorMessage="{{ $errors->first('applicationNotes') }}"></x-textarea>
                </div>

                <div>
                    <x-input-label>Referred By</x-input-label>
                    <x-select wire:model="referedBy" errorMessage="{{ $errors->first('referedBy') }}">
                        <option value="">-- Select Employee (Optional) --</option>
                        @foreach ($referedByOptions as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
            @else
                <div class="alert alert-warning">
                    There are no available vacancies for this applicant to apply to. Either all vacancies are closed or
                    the applicant has already applied to all available positions.
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeNewApplicationModal">Cancel</x-secondary-button>
                <x-primary-button wire:click.prevent="createApplication" loadingFunction="createApplication">
                    Submit Application
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

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
</div>
