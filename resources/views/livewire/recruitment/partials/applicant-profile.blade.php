<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left column - Profile Image and Key Details -->
        <div>
            <div class="card mb-6">
                <div class="card-body text-center p-4">
                    @if ($applicant->image_url)
                        <div class="mb-4">
                            <img src="{{ $applicant->full_image_url }}" alt="{{ $applicant->full_name }}"
                                class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-white shadow">
                        </div>
                    @else
                        <div class="mb-4">
                            <div
                                class="w-32 h-32 rounded-full bg-slate-200 flex items-center justify-center mx-auto border-4 border-white shadow">
                                <i class="fas fa-user text-4xl text-slate-400"></i>
                            </div>
                        </div>
                    @endif
                    <h4 class="font-medium text-xl mb-1">{{ $applicant->full_name }}</h4>
                    <p class="text-slate-500 mb-3">{{ $applicant->email }}</p>

                    <div class="flex justify-center space-x-2">
                        <a href="mailto:{{ $applicant->email }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-envelope mr-1"></i> Email
                        </a>
                        @if ($applicant->phone)
                            <a href="tel:{{ $applicant->phone }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-phone-alt mr-1"></i> Call
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3">
                    <h5 class="font-medium m-0">Contact Information</h5>
                </div>
                <div class="card-body mt-2">
                    <ul class="space-y-3">
                        <li class="flex">
                            <div class="w-10 text-slate-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-slate-500">Email</p>
                                <p class="font-medium">{{ $applicant->email }}</p>
                            </div>
                        </li>
                        @if ($applicant->phone)
                            <li class="flex">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-500">Mobile</p>
                                    <p class="font-medium">{{ $applicant->phone }}</p>
                                </div>
                            </li>
                        @endif
                        @if ($applicant->home_phone)
                            <li class="flex">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-500">Home Phone</p>
                                    <p class="font-medium">{{ $applicant->home_phone }}</p>
                                </div>
                            </li>
                        @endif

                        @if ($applicant->area)
                            <li class="flex">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-500">Area</p>
                                    <p class="font-medium">{{ $applicant->area->name }}</p>
                                </div>
                            </li>
                        @endif


                        @if ($applicant->address)
                            <li class="flex">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-500">Address</p>
                                    <p class="font-medium">{{ $applicant->address }}</p>
                                </div>
                            </li>
                        @endif

                        @if ($applicant->channel)
                            <li class="flex">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-slate-500">Channel</p>
                                    <p class="font-medium">{{ $applicant->channel->name }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3">
                    <h5 class="font-medium m-0">Documents</h5>
                </div>
                <div class="card-body mt-2">
                    <ul class="space-y-3">
                        @if ($applicant->cv_url)
                            <li class="flex items-center">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">Resume/CV</p>
                                </div>
                                <div>
                                    <a href="{{ Storage::url($applicant->cv_url) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </li>
                        @endif

                        @foreach ($applicant->documents as $document)
                            <li class="flex items-center">
                                <div class="w-10 text-slate-400">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">{{ $document->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $document->created_at->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <a href="{{ $document->full_path }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary ">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach

                        @if (!$applicant->cv_url && count($applicant->documents) === 0)
                            <li class="text-center py-3">
                                <p class="text-slate-500">No documents available</p>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-4">
                        <button type="button" class="btn btn-sm btn-primary w-full"
                            wire:click="openDocumentUploadModal">
                            <i class="fas fa-upload mr-1"></i> Upload Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle column - Personal Details , Education and Health Issues -->
        <div>
            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Personal Details</h5>
                </div>
                <div class="card-body mt-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Full Name</p>
                            <p class="font-medium">{{ $applicant->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Gender</p>
                            <p class="font-medium">{{ $applicant->gender }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Birth Date</p>
                            <p class="font-medium">
                                {{ $applicant->birth_date ? $applicant->birth_date->format('d M Y') : 'Not specified' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Nationality</p>
                            <p class="font-medium">{{ $applicant->nationality ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Marital Status</p>
                            <p class="font-medium">{{ $applicant->marital_status ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">ID Number</p>
                            <p class="font-medium">{{ $applicant->social_number ?? 'Not specified' }}</p>
                        </div>
                        @if ($applicant->military_status)
                            <div>
                                <p class="text-sm text-slate-500">Military Status</p>
                                <p class="font-medium">{{ $applicant->military_status }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-slate-500">Application Channel</p>
                            <p class="font-medium">{{ $applicant->application_channel ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Education</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->educations) > 0)
                        <div class="divide-y">
                            @foreach ($applicant->educations as $education)
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <h6 class="font-medium">{{ $education->degree }}</h6>
                                    </div>
                                    <p class="text-slate-500">{{ $education->field_of_study }}</p>
                                    <p class="text-slate-600">{{ $education->school_name }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $education->start_date->format('M Y') }} -
                                        {{ $education->end_date ? $education->end_date->format('M Y') : 'Present' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No education history added</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Training</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->trainings) > 0)
                        <div class="divide-y">
                            @foreach ($applicant->trainings as $training)
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <h6 class="font-medium">{{ $training->name }}</h6>
                                    </div>
                                    <p class="text-slate-600">{{ $training->organization }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $training->start_date->format('M Y') }} · {{ $training->duration }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No training history added</p>
                        </div>
                    @endif
                </div>
            </div>

            @if ($applicant->health?->has_health_issues)
                <div class="card mb-6 p-4">
                    <div class="card-header py-3 flex justify-between items-center">
                        <h5 class="font-medium m-0">Health Issues</h5>
                    </div>
                    <div class="card-body mt-2">
                        <div class="p-4 text-center">
                            <p class="text-slate-500">
                                {{ $applicant->health->health_issues }}
                            </p>
                        </div>

                    </div>
                </div>
            @endif
        </div>

        <!-- Right column - Experience, Languages, Skills and References -->
        <div>
            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Work Experience</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->experiences) > 0)
                        <div class="divide-y">
                            @foreach ($applicant->experiences as $experience)
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <h6 class="font-medium">{{ $experience->position }}</h6>
                                    </div>
                                    <p class="text-slate-600">{{ $experience->company_name }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ $experience->start_date->format('M Y') }} -
                                        {{ $experience->end_date ? $experience->end_date->format('M Y') : 'Present' }}
                                    </p>
                                    @if ($experience->salary)
                                        <p class="text-sm text-slate-500 mt-1">
                                            Salary: {{ $experience->formatted_salary }}
                                        </p>
                                    @endif
                                    @if ($experience->leaving_reason)
                                        <div class="mt-2">
                                            <p class="text-sm text-slate-500">Reason for leaving:</p>
                                            <p class="text-sm text-slate-600">{{ $experience->leaving_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No work experience added</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Languages</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->languages) > 0)
                        <div class="divide-y">
                            @foreach ($applicant->languages as $language)
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <h6 class="font-medium">{{ $language->language }}</h6>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <div>
                                            <p class="text-sm text-slate-500">Reading</p>
                                            <div class="mt-1">
                                                <span class="badge bg-primary">{{ $language->reading_level }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Writing</p>
                                            <div class="mt-1">
                                                <span class="badge bg-primary">{{ $language->writing_level }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500">Speaking</p>
                                            <div class="mt-1">
                                                <span class="badge bg-primary">{{ $language->speaking_level }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No languages added</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">Skills</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->skills) > 0)
                        <div class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($applicant->skills as $skill)
                                    <div class="bg-slate-100 rounded-full px-3 py-1 flex items-center">
                                        <span class="mr-2">{{ $skill->skill }} ({{ $skill->level }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No skills added</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-6 p-4">
                <div class="card-header py-3 flex justify-between items-center">
                    <h5 class="font-medium m-0">References</h5>
                </div>
                <div class="card-body mt-2">
                    @if (count($applicant->references) > 0)
                        <div class="divide-y">
                            @foreach ($applicant->references as $references)
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <h6 class="font-medium">{{ $references->name }}</h6>
                                        <p class="text-slate-500">{{ $references->relationship }}</p>
                                    </div>
                                    <p class="text-slate-500"><a href="mailto:{{ $references->email }}">{{ $references->email }}</a></p>
                                    <p class="text-slate-600"><a href="tel:{{ $references->phone }}">{{ $references->phone }}</a></p>
                                    <p class="text-sm text-slate-500">
                                        {{ $references->address }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-slate-500">No education history added</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal wire:model="showDocumentUploadModal">
        <x-slot name="title">Upload Document</x-slot>
        <div class="modal-body">
            <div class="mb-3">
                <label for="document_name" class="form-label">Document Name</label>
                <input type="text" id="document_name" class="form-control" wire:model="documentName"
                    placeholder="e.g. ID Card, Certificate, etc.">
                @error('documentName')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_file" class="form-label">Document File</label>
                <input type="file" id="document_file" class="form-control" wire:model="documentFile">
                <div wire:loading wire:target="documentFile" class="text-primary mt-1">
                    <i class="fas fa-spinner fa-spin"></i> Uploading...
                </div>
                @error('documentFile')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_notes" class="form-label">Notes (Optional)</label>
                <textarea id="document_notes" class="form-control" wire:model="documentNotes" rows="2"
                    placeholder="Any notes about this document"></textarea>
                @error('documentNotes')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="mt-4 flex justify-end gap-3">
                <x-secondary-button wire:click="closeDocumentUploadModal">Cancel</x-secondary-button>
                <x-primary-button wire:click="uploadDocument" loadingFunction="uploadDocument">Upload Document
                </x-primary-button>
            </div>
        </x-slot>
    </x-modal>

</div>
