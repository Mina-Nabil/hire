<div class="space-y-6">
    <!-- Applicants -->
    <div>
        <header class="card-header cust-card-header noborder">

            <div class="flex items-center justify-between items-center w-full gap-2">

                <h4 class="text-lg font-medium mb-4">Applicants</h4>
                <div>

                    <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                        icon="line-md:loading-twotone-loop"></iconify-icon>
                    <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="Search"
                        wire:model.live.debounce.400ms="search">
                </div>
            </div>

        </header>

        @if ($applicants->count() > 0)
            <div class="card">
                <div class="card-body px-6 pb-6">
                    <div class="-mx-6">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead
                                    class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Applicant</th>
                                        <th scope="col" class="table-th">Email</th>
                                        <th scope="col" class="table-th">Phone</th>
                                        <th scope="col" class="table-th">Application Date</th>
                                        <th scope="col" class="table-th">Status</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($applicants as $applicant)
                                        @php
                                            $application = $applicant->applications
                                                ->where('vacancy_id', $vacancy->id)
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td class="table-td">
                                                <div class="flex items-center">
                                                    @if ($applicant->image_url)
                                                        <img src="{{ Storage::url($applicant->image_url) }}"
                                                            alt="{{ $applicant->full_name }}"
                                                            class="h-8 w-8 rounded-full mr-2">
                                                    @else
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm mr-2">
                                                            {{ substr($applicant->first_name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium text-sm">{{ $applicant->full_name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="table-td">{{ $applicant->email }}</td>
                                            <td class="table-td">{{ $applicant->phone }}</td>
                                            <td class="table-td">{{ $application->created_at->format('d M Y') }}</td>
                                            <td class="table-td">
                                                <span
                                                    class="badge {{ $application->status == 'pending' ? 'bg-warning-200' : ($application->status == 'rejected' ? 'bg-danger-200' : 'bg-success-200') }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <div class="flex items-center gap-2">
                                                    <a class="btn btn-sm btn-secondary"
                                                        wire:click="showApplicant({{ $applicant->id }})">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </a>
                                                    <a class="btn btn-sm btn-primary"
                                                        wire:click="openNewInterviewModal({{ $applicant->id }})">
                                                        <i class="fas fa-calendar-alt mr-1"></i> Schedule Interview
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="position: sticky; bottom:0;width:100%; z-index:10;"
                                class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                {{ $applicants->links('vendor.livewire.simple-bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <div class="text-slate-400 mb-3">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <h5 class="font-medium text-lg mb-1">No Applicants</h5>
                    <p class="text-slate-500">This vacancy doesn't have any applicants yet.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Interviews -->
    <div>
        <h4 class="text-lg font-medium mb-4">Interviews</h4>

        @if ($interviews->count() > 0)
            <div class="card">
                <div class="card-body px-6 pb-6">
                    <div class="-mx-6">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead
                                    class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Applicant</th>
                                        <th scope="col" class="table-th">Date & Time</th>
                                        <th scope="col" class="table-th">Type</th>
                                        <th scope="col" class="table-th">Location</th>
                                        <th scope="col" class="table-th">Status</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($interviews as $interview)
                                        <tr>
                                            <td class="table-td">{{ $interview->application->applicant->full_name }}
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
                                                    <button class="inline-flex justify-center items-center"
                                                        type="button" id="interviewDropdown{{ $interview->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[29990] float-left list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none"
                                                        aria-labelledby="interviewDropdown{{ $interview->id }}">
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openFeedbackModal({{ $interview->id }})">
                                                                <i class="fas fa-comment-dots mr-2"></i> Provide
                                                                Feedback
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openShowFeedbacksModal({{ $interview->id }})">
                                                                <i class="fas fa-history mr-2"></i> View Feedbacks
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openSetInterviewersModal({{ $interview->id }})">
                                                                <i class="fas fa-users mr-2"></i> Assign Interviewers
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openRescheduleModal({{ $interview->id }})"
                                                                @if (in_array($interview->status, ['completed', 'cancelled'])) disabled @endif>
                                                                <i class="fas fa-calendar-alt mr-2"></i> Reschedule
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openUpdateStatusModal({{ $interview->id }})">
                                                                <i class="fas fa-sync-alt mr-2"></i> Update Status
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openAddNoteModal({{ $interview->id }})">
                                                                <i class="fas fa-sticky-note mr-2"></i> Add Note
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openCompleteModal({{ $interview->id }})"
                                                                @if (!in_array($interview->status, ['scheduled', 'rescheduled'])) disabled @endif>
                                                                <i class="fas fa-check mr-2"></i> Mark as Completed
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="hover:bg-slate-900 dark:hover:bg-slate-600 dark:hover:bg-opacity-70 hover:text-white w-full border-b border-b-gray-500 border-opacity-10 px-4 py-2 text-sm dark:text-slate-300 last:mb-0 cursor-pointer first:rounded-t last:rounded-b flex space-x-2 items-center capitalize rtl:space-x-reverse"
                                                                wire:click="openCancelModal({{ $interview->id }})"
                                                                @if (in_array($interview->status, ['completed', 'cancelled'])) disabled @endif>
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
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-8">
                    <div class="text-slate-400 mb-3">
                        <i class="fas fa-calendar-alt text-4xl"></i>
                    </div>
                    <h5 class="font-medium text-lg mb-1">No Interviews Scheduled</h5>
                    <p class="text-slate-500">There are no interviews scheduled for this vacancy yet.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Job Offers -->
    <div>
        <h4 class="text-lg font-medium mb-4">Job Offers</h4>

        @if ($offers->count() > 0)
            <div class="card">
                <div class="card-body px-6 pb-6">
                    <div class="-mx-6">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead
                                    class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Applicant</th>
                                        <th scope="col" class="table-th">Offered Salary</th>
                                        <th scope="col" class="table-th">Proposed Start Date</th>
                                        <th scope="col" class="table-th">Expiry Date</th>
                                        <th scope="col" class="table-th">Status</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach ($offers as $offer)
                                        <tr>
                                            <td class="table-td">{{ $offer->application->applicant->full_name }}</td>
                                            <td class="table-td">{{ $offer->offered_salary }}</td>
                                            <td class="table-td">{{ $offer->proposed_start_date->format('d M Y') }}
                                            </td>
                                            <td class="table-td">{{ $offer->expiry_date->format('d M Y') }}</td>
                                            <td class="table-td">
                                                <span
                                                    class="badge {{ $offer->status == 'Accepted' ? 'bg-success-200' : ($offer->status == 'Rejected' ? 'bg-danger-200' : 'bg-warning-200') }}">
                                                    {{ $offer->status }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <a href="{{ route('recruitment.applicants.show', $offer->application->applicant->id) }}"
                                                    class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
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
            <div class="card">
                <div class="card-body text-center py-8">
                    <div class="text-slate-400 mb-3">
                        <i class="fas fa-file-contract text-4xl"></i>
                    </div>
                    <h5 class="font-medium text-lg mb-1">No Job Offers</h5>
                    <p class="text-slate-500">No job offers have been made for this vacancy yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
