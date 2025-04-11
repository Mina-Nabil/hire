<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Interviews</h3>
    </div>
    <div class="card">
        @if ($interviews->count() > 0)
            <div class="card-body px-6 pb-6">
                <div class=" -mx-6">
                    <div class="inline-block min-w-full align-middle">

                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead
                                class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class=" table-th">Position</th>
                                    <th scope="col" class=" table-th">Date & Time</th>
                                    <th scope="col" class=" table-th">Type</th>
                                    <th scope="col" class=" table-th">Location</th>
                                    <th scope="col" class=" table-th">Status</th>
                                    <th scope="col" class=" table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @foreach ($interviews as $interview)
                                    <tr>
                                        <td class="table-td">{{ $interview->application->vacancy->position->name }}
                                        </td>
                                        <td class="table-td">{{ $interview->date->format('d M Y') }} at
                                            {{ $interview->date->format('H:i') }}</td>
                                        <td class="table-td">{{ $interview->type }}</td>
                                        <td class="table-td">{{ $interview->location }}</td>
                                        <td class="table-td">
                                            <span class="badge {{ $interview->status_class }}">
                                                {{ $interview->status }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="dropstart relative">
                                                <button class="inline-flex justify-center items-center" type="button"
                                                    id="interviewDropdown{{ $interview->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[29990] float-left list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                                                    aria-labelledby="interviewDropdown{{ $interview->id }}">
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openFeedbackModal({{ $interview->id }})">
                                                            <i class="fas fa-comment-dots mr-2"></i> Provide Feedback
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openShowFeedbacksModal({{ $interview->id }})">
                                                            <i class="fas fa-history mr-2"></i> View Feedbacks
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openSetInterviewersModal({{ $interview->id }})">
                                                            <i class="fas fa-users mr-2"></i> Assign
                                                            Interviewers
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openRescheduleModal({{ $interview->id }})"
                                                            @if (in_array($interview->status, ['Completed', 'Cancelled'])) disabled @endif>
                                                            <i class="fas fa-calendar-alt mr-2"></i> Reschedule
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openUpdateStatusModal({{ $interview->id }})">
                                                            <i class="fas fa-sync-alt mr-2"></i> Update Status
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openAddNoteModal({{ $interview->id }})">
                                                            <i class="fas fa-sticky-note mr-2"></i> Add Note
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openCompleteModal({{ $interview->id }})"
                                                            @if (!in_array($interview->status, ['Scheduled', 'In Progress'])) disabled @endif>
                                                            <i class="fas fa-check mr-2"></i> Mark as Completed
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300  last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize  rtl:space-x-reverse"
                                                            wire:click="openCancelModal({{ $interview->id }})"
                                                            @if (in_array($interview->status, ['Completed', 'Cancelled'])) disabled @endif>
                                                            <i class="fas fa-times mr-2"></i> Cancel Interview
                                                        </a>
                                                    </li>
                                                </ul>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        @else
            <div class="card-body text-center py-8">
                <div class="text-slate-400 mb-3">
                    <i class="fas fa-calendar-alt text-4xl"></i>
                </div>
                <h5 class="font-medium text-lg mb-1">No Interviews Scheduled</h5>
                <p class="text-slate-500">This applicant doesn't have any interviews scheduled yet.</p>
            </div>
        @endif
    </div>

       <!-- Include all the interview management modals -->
       @include('livewire.recruitment.partials.interview-modals')
</div>
