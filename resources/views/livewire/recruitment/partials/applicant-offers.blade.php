<div>

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Offers</h3>
    </div>
    <div class="card">

        @if ($offers->count() > 0)
            <div class="card-body px-6 pb-6">
                <div class="-mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead
                                class="border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class=" table-th">Position</th>
                                    <th scope="col" class=" table-th">Department</th>
                                    <th scope="col" class=" table-th">Salary</th>
                                    <th scope="col" class=" table-th">Sent</th>
                                    <th scope="col" class=" table-th">Expiry</th>
                                    <th scope="col" class=" table-th">Start</th>
                                    <th scope="col" class=" table-th">Status</th>
                                    <th scope="col" class=" table-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td class="table-td">{{ $offer->application->vacancy->position->title }}</td>
                                        <td class="table-td">{{ $offer->application->vacancy->position->department->name }}
                                        </td>
                                        <td class="table-td">{{ $offer->formatted_salary }}</td>
                                        <td class="table-td">{{ $offer->offer_date ? $offer->offer_date->format('d M Y') : 'Not sent' }}
                                        </td>
                                        <td class="table-td">{{ $offer->expiry_date ? $offer->expiry_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td class="table-td">{{ $offer->proposed_start_date ? $offer->proposed_start_date->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td class="table-td">
                                            <span class="badge {{ $offer->status_class }}">
                                                {{ $offer->status }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex space-x-2">
                                                <button type="button" class="btn btn-xs btn-outline-primary"
                                                    wire:click="openEditOfferModal({{ $offer->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-info"
                                                    wire:click="$dispatch('showConfirmation', {
                                                        title: 'Send Offer',
                                                        message: 'Are you sure you want to send this offer?',
                                                        color: 'info',
                                                        callback: 'sendOffer',
                                                        params: {{ $offer->id }},
                                                    })">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-success"
                                                    wire:click="openAcceptOfferModal({{ $offer->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-danger"
                                                    wire:click="openRejectOfferModal({{ $offer->id }})">
                                                    <i class="fas fa-times"></i>
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
        @else
            <div class="card-body text-center py-8">
                <div class="text-slate-400 mb-3">
                    <i class="fas fa-file-contract text-4xl"></i>
                </div>
                <h5 class="font-medium text-lg mb-1">No Offers Created</h5>
                <p class="text-slate-500">No job offers have been created for this applicant yet.</p>
            </div>
        @endif
    </div>
</div>
