<div class="card">
    <div class="card-header">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 mb-2">
            Create New Employee
        </h4>
        <div class="flex justify-end">
            <button type="button" wire:click="openApplicantModal" class="btn btn-primary btn-sm">
                <i class="fas fa-user-tie mr-1"></i> Select Applicant
            </button>
        </div>
    </div>
    <div class="card-body p-6">
        @if ($applicant_id)
            <div
                class="py-[18px] px-6 font-normal text-sm rounded-md bg-white text-success-500 border border-success-500
                                    dark:bg-slate-800 mb-5">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <iconify-icon class="text-2xl flex-0" icon="heroicons:check-circle"></iconify-icon>
                    <p class="flex-1 font-Inter">
                        Data pre-filled from applicant record.
                    </p>
                    <div class="flex-0 text-xl cursor-pointer">
                        <iconify-icon icon="line-md:close"></iconify-icon>
                    </div>
                </div>
            </div>
        @endif
        <form wire:submit.prevent="createEmployee">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Basic Employee Information Section -->
                <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-md">
                    <h5 class="font-medium text-xl text-slate-900 dark:text-white mb-5">
                        Basic Information
                    </h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Associated User -->
                        <div class="input-area">
                            <label for="user_id" class="form-label">User Account</label>
                            <select id="user_id" class="form-control @error('user_id') !border-danger-500 @enderror"
                                wire:model="user_id">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="input-area">
                            <label for="name" class="form-label">Full Name</label>
                            <input id="name" type="text"
                                class="form-control @error('name') !border-danger-500 @enderror" wire:model="name">
                            @error('name')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="input-area">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email"
                                class="form-control @error('email') !border-danger-500 @enderror" wire:model="email">
                            @error('email')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="input-area">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" type="text"
                                class="form-control @error('phone') !border-danger-500 @enderror" wire:model="phone">
                            @error('phone')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="input-area">
                            <label for="address" class="form-label">Address</label>
                            <input id="address" type="text"
                                class="form-control @error('address') !border-danger-500 @enderror"
                                wire:model="address">
                            @error('address')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="input-area">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input id="nationality" type="text"
                                class="form-control @error('nationality') !border-danger-500 @enderror"
                                wire:model="nationality">
                            @error('nationality')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="input-area">
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" class="form-control @error('gender') !border-danger-500 @enderror"
                                wire:model="gender">
                                <option value="">Select Gender</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender }}">{{ $gender }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="input-area">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input id="birth_date" type="date"
                                class="form-control @error('birth_date') !border-danger-500 @enderror"
                                wire:model="birth_date">
                            @error('birth_date')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Birth Place -->
                        <div class="input-area">
                            <label for="birth_place_id" class="form-label">Birth Place</label>
                            <select id="birth_place_id"
                                class="form-control @error('birth_place_id') !border-danger-500 @enderror"
                                wire:model="birth_place_id">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('birth_place_id')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- License Required -->
                        <div class="input-area">
                            <label for="license_required" class="form-label">License Required</label>
                            <div class="checkbox-area">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="hidden" name="checkbox"
                                        wire:model="license_required">
                                    <span
                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                        <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                    <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">Employee
                                        requires
                                        a driver license ?</span>
                                </label>
                            </div>
                            @error('license_required')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Employment Date -->
                        <div class="input-area">
                            <label for="employment_date" class="form-label">Employment Date</label>
                            <input id="employment_date" type="date"
                                class="form-control @error('employment_date') !border-danger-500 @enderror"
                                wire:model="employment_date">
                            @error('employment_date')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employee Additional Information Section -->
                <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-md">
                    <h5 class="font-medium text-xl text-slate-900 dark:text-white mb-5">
                        Additional Information
                    </h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Insurance Office -->
                        <div class="input-area">
                            <label for="insurance_office_id" class="form-label">Insurance Office</label>
                            <select id="insurance_office_id" class="form-control" wire:model="insurance_office_id">
                                <option value="">Select Insurance Office</option>
                                @foreach ($insuranceOffices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @error('insurance_office_id')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Insurance Number -->
                        <div class="input-area">
                            <label for="insurance_number" class="form-label">Insurance Number</label>
                            <input id="insurance_number" type="text"
                                class="form-control @error('insurance_number') !border-danger-500 @enderror"
                                wire:model="insurance_number">
                            @error('insurance_number')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Insurance Amount -->
                        <div class="input-area">
                            <label for="insurance_amount" class="form-label">Insurance Amount</label>
                            <input id="insurance_amount" type="number" step="0.01"
                                class="form-control @error('insurance_amount') !border-danger-500 @enderror"
                                wire:model="insurance_amount">
                            @error('insurance_amount')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Academic Qualification -->
                        <div class="input-area">
                            <label for="academic_qualification" class="form-label">Academic Qualification</label>
                            <input id="academic_qualification" type="text"
                                class="form-control @error('academic_qualification') !border-danger-500 @enderror"
                                wire:model="academic_qualification">
                            @error('academic_qualification')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- University -->
                        <div class="input-area">
                            <label for="university" class="form-label">University</label>
                            <input id="university" type="text"
                                class="form-control @error('university') !border-danger-500 @enderror"
                                wire:model="university">
                            @error('university')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Graduation Year -->
                        <div class="input-area">
                            <label for="graduation_year" class="form-label">Graduation Year</label>
                            <input id="graduation_year" type="number" min="1900" max="2100"
                                class="form-control @error('graduation_year') !border-danger-500 @enderror"
                                wire:model="graduation_year">
                            @error('graduation_year')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Military Status -->
                        <div class="input-area">
                            <label for="military_status" class="form-label">Military Status</label>
                            <select id="military_status"
                                class="form-control @error('military_status') !border-danger-500 @enderror"
                                wire:model="military_status">
                                <option value="">Select Military Status</option>
                                @foreach ($militaryStatuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('military_status')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div class="input-area">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select id="marital_status"
                                class="form-control @error('marital_status') !border-danger-500 @enderror"
                                wire:model="marital_status">
                                <option value="">Select Marital Status</option>
                                @foreach ($maritalStatuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('marital_status')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" class="btn btn-danger mr-3 btn-sm" wire:click="resetForm">
                    <i class="fas fa-undo mr-1"></i> Reset Form
                </button>
                <button type="submit" class="btn btn-success ml-3 btn-sm">Create Employee</button>
            </div>
        </form>
    </div>

    <!-- Applicant Selection Modal -->
    <x-modal wire:model="showApplicantModal">
        <x-slot name="title">Select Applicant with Accepted Offer</x-slot>
        <div class="modal-body">
            <div class="mb-4">
                <div class="input-area">
                    <label for="applicant_search" class="form-label">Search Applicants</label>
                    <div class="relative">
                        <input type="text" id="applicant_search" class="form-control pl-10"
                            wire:model.debounce.200ms="applicantSearch" wire:keyup="loadApplicantsWithOffers"
                            placeholder="Search by name, email or phone...">
                    </div>
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @if (count($applicantsWithOffers) > 0)
                    <div class="divide-y">
                        @foreach ($applicantsWithOffers as $applicant)
                            <div class="p-3 hover:bg-slate-50 cursor-pointer"
                                wire:click="selectApplicant({{ $applicant->id }})">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if ($applicant->image_url)
                                            <img class="h-10 w-10 rounded-full"
                                                src="{{ $applicant->full_image_url }}"
                                                alt="{{ $applicant->full_name }}">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">
                                                <i class="fas fa-user text-slate-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-slate-900">{{ $applicant->full_name }}</div>
                                        <div class="text-sm text-slate-500">
                                            {{ $applicant->email }} | {{ $applicant->phone }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-1">
                                            @if ($applicant->applications->isNotEmpty())
                                                Applied for:
                                                {{ $applicant->applications->collect()->first()->vacancy->position->name ?? 'Unknown Position' }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($applicant_id == $applicant->id)
                                        <div class="ml-auto">
                                            <span class="badge bg-success-500 text-white">Selected</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-slate-400 mb-2">
                            <i class="fas fa-user-slash text-3xl"></i>
                        </div>
                        <p class="text-slate-500">No applicants with accepted offers found</p>
                    </div>
                @endif
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="closeApplicantModal">
                    Cancel
                </button>
            </div>
        </x-slot>
    </x-modal>

</div>
