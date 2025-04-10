<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Offers</h3>
        <button type="button" class="btn btn-primary btn-sm" wire:click="openNewOfferModal"
            @if (!$canCreateOffer) disabled @endif>
            <i class="fas fa-plus-circle mr-1"></i> Create Offer
        </button>
    </div>

    @if ($offers->count() > 0)
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Offered Salary</th>
                        <th>Date Sent</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($offers as $offer)
                        <tr>
                            <td>{{ $offer->application->vacancy->position->title }}</td>
                            <td>{{ $offer->application->vacancy->position->department->name }}</td>
                            <td>{{ $offer->formatted_salary }}</td>
                            <td>{{ $offer->offer_date ? $offer->offer_date->format('d M Y') : 'Not sent' }}</td>
                            <td>{{ $offer->expiry_date ? $offer->expiry_date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $offer->status_class }}">
                                    {{ $offer->status }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="viewOffer({{ $offer->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                        wire:click="sendOffer({{ $offer->id }})"
                                        @if (!in_array($offer->status, ['Draft'])) disabled @endif>
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                        wire:click="acceptOffer({{ $offer->id }})"
                                        @if (!in_array($offer->status, ['Sent'])) disabled @endif>
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        wire:click="rejectOffer({{ $offer->id }})"
                                        @if (!in_array($offer->status, ['Sent'])) disabled @endif>
                                        <i class="fas fa-times"></i>
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
                    <i class="fas fa-file-contract text-4xl"></i>
                </div>
                <h5 class="font-medium text-lg mb-1">No Offers Created</h5>
                <p class="text-slate-500">No job offers have been created for this applicant yet.</p>
            </div>
        </div>
    @endif

    <!-- New Offer Modal -->
    @if ($showNewOfferModal)
        <div class="modal show" tabindex="-1" role="dialog"
            style="display: block ;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Job Offer</h5>
                        <button type="button" class="btn-close" wire:click="closeNewOfferModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="application_id" class="form-label">Select Application</label>
                            <select id="application_id" class="form-select" wire:model="applicationId">
                                <option value="">-- Select application --</option>
                                @foreach ($eligibleApplications as $application)
                                    <option value="{{ $application->id }}">
                                        {{ $application->vacancy->position->title }} -
                                        {{ $application->vacancy->position->department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('applicationId')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="offered_salary" class="form-label">Offered Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="offered_salary" class="form-control"
                                    wire:model="offeredSalary" step="100">
                            </div>
                            @error('offeredSalary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="proposed_start_date" class="form-label">Proposed Start Date</label>
                            <input type="date" id="proposed_start_date" class="form-control"
                                wire:model="proposedStartDate">
                            @error('proposedStartDate')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Offer Expiry Date</label>
                            <input type="date" id="expiry_date" class="form-control" wire:model="expiryDate">
                            @error('expiryDate')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Benefits Package</label>
                            <textarea id="benefits" class="form-control" wire:model="benefits" rows="3"
                                placeholder="Detail the benefits included with this offer"></textarea>
                            @error('benefits')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="special_terms" class="form-label">Special Terms & Conditions (Optional)</label>
                            <textarea id="special_terms" class="form-control" wire:model="specialTerms" rows="3"
                                placeholder="Any special terms or conditions for this offer"></textarea>
                            @error('specialTerms')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="offer_notes" class="form-label">Internal Notes (Not shared with
                                candidate)</label>
                            <textarea id="offer_notes" class="form-control" wire:model="offerNotes" rows="2"
                                placeholder="Notes for internal reference only"></textarea>
                            @error('offerNotes')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="closeNewOfferModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="createOffer"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="createOffer"
                                class="spinner-border spinner-border-sm mr-1"></span>
                            Create Offer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- View Offer Modal -->
    @if ($showViewOfferModal)
        <div class="modal show" tabindex="-1" role="dialog"
            style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Job Offer</h5>
                        <button type="button" class="btn-close" wire:click="closeViewOfferModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedOffer)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-slate-500">Position</p>
                                            <p class="font-medium">
                                                {{ $selectedOffer->application->vacancy->position->title }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Department</p>
                                            <p class="font-medium">
                                                {{ $selectedOffer->application->vacancy->position->department->name }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Offered Salary</p>
                                            <p class="font-medium">{{ $selectedOffer->formatted_salary }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Status</p>
                                            <p class="font-medium">
                                                <span class="badge {{ $selectedOffer->status_class }}">
                                                    {{ $selectedOffer->status }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Created Date</p>
                                            <p class="font-medium">{{ $selectedOffer->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Offer Sent Date</p>
                                            <p class="font-medium">
                                                {{ $selectedOffer->offer_date ? $selectedOffer->offer_date->format('d M Y') : 'Not sent yet' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Expiry Date</p>
                                            <p class="font-medium">
                                                {{ $selectedOffer->expiry_date ? $selectedOffer->expiry_date->format('d M Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Proposed Start Date</p>
                                            <p class="font-medium">
                                                {{ $selectedOffer->proposed_start_date ? $selectedOffer->proposed_start_date->format('d M Y') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="font-medium mb-2">Benefits Package</h6>
                                <div class="p-3 bg-slate-50 rounded">
                                    {!! nl2br(e($selectedOffer->benefits)) !!}
                                </div>
                            </div>

                            @if ($selectedOffer->special_terms)
                                <div class="mb-4">
                                    <h6 class="font-medium mb-2">Special Terms & Conditions</h6>
                                    <div class="p-3 bg-slate-50 rounded">
                                        {!! nl2br(e($selectedOffer->special_terms)) !!}
                                    </div>
                                </div>
                            @endif

                            @if ($selectedOffer->notes)
                                <div class="mb-4">
                                    <h6 class="font-medium mb-2">Internal Notes</h6>
                                    <div class="p-3 bg-slate-50 rounded border-l-4 border-amber-400">
                                        <span class="text-sm text-amber-600 mb-1 block">Not visible to candidate</span>
                                        {!! nl2br(e($selectedOffer->notes)) !!}
                                    </div>
                                </div>
                            @endif

                            @if ($selectedOffer->response_notes)
                                <div class="mb-4">
                                    <h6 class="font-medium mb-2">Candidate Response</h6>
                                    <div class="p-3 bg-slate-50 rounded border-l-4 border-blue-400">
                                        {!! nl2br(e($selectedOffer->response_notes)) !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="closeViewOfferModal">Close</button>
                        @if ($selectedOffer && $selectedOffer->status === 'Draft')
                            <button type="button" class="btn btn-primary"
                                wire:click="sendOffer({{ $selectedOffer->id }})" wire:loading.attr="disabled">
                                <span wire:loading wire:target="sendOffer"
                                    class="spinner-border spinner-border-sm mr-1"></span>
                                Send Offer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
