<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Offers</h3>
        <button type="button" class="btn btn-primary" wire:click="openNewOfferModal"
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

    <!-- View Offer Modal -->

</div>
