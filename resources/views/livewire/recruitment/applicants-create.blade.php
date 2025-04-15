<div>
    <div class="flex justify-between flex-wrap items-center mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
            Create New Applicant
        </h4>
    </div>

    <div class="card">
        <!-- Progress Steps -->
        <div class="p-5">
            <div class="flex justify-between mb-3">
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <div
                        class="step-item {{ $currentStep >= $i ? 'active' : '' }} {{ $currentStep > $i ? 'completed' : '' }}">
                        <div class="step-number">{{ $i }}</div>
                        <div class="step-title hidden md:block">
                            @switch($i)
                                @case(1)
                                    Personal Info
                                @break

                                @case(2)
                                    Education
                                @break

                                @case(3)
                                    Training
                                @break

                                @case(4)
                                    Experience
                                @break

                                @case(5)
                                    Languages
                                @break

                                @case(6)
                                    References
                                @break

                                @case(7)
                                    Skills & Health
                                @break

                                @case(8)
                                    Vacancy
                                @break
                            @endswitch
                        </div>

                    </div>
                @endfor
            </div>
        </div>

        <!-- Form Content -->
        <div class="card-body p-6">
            <form wire:submit.prevent="{{ $currentStep < $totalSteps ? 'nextStep' : 'createApplicant' }}">
                <!-- Step 1: Personal Information -->
                @if ($currentStep === 1)
                    <h4 class="text-xl font-medium mb-5">Personal Information</h4>
                    <div class="grid  lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-6">
                        <!-- First Name -->
                        <div>
                            <div class="form-group">
                                <label for="firstName" class="form-label">First Name <span
                                        class="text-danger-500">*</span></label>
                                <input type="text" id="firstName" wire:model="firstName"
                                    class="form-control @error('firstName') !border-danger-500 @enderror">
                                @error('firstName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Middle Name -->
                        <div>
                            <div class="form-group">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" id="middleName" wire:model="middleName"
                                    class="form-control @error('middleName') !border-danger-500 @enderror">
                                @error('middleName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Last Name -->
                        <div>
                            <div class="form-group">
                                <label for="lastName" class="form-label">Last Name <span
                                        class="text-danger-500">*</span></label>
                                <input type="text" id="lastName" wire:model="lastName"
                                    class="form-control @error('lastName') !border-danger-500 @enderror">
                                @error('lastName')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email <span
                                    class="text-danger-500">*</span></label>
                            <input type="email" id="email" wire:model="email"
                                class="form-control @error('email') !border-danger-500 @enderror">
                            @error('email')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone <span
                                    class="text-danger-500">*</span></label>
                            <input type="text" id="phone" wire:model="phone"
                                class="form-control @error('phone') !border-danger-500 @enderror">
                            @error('phone')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Home Phone -->
                        <div class="form-group">
                            <label for="homePhone" class="form-label">Home Phone</label>
                            <input type="text" id="homePhone" wire:model="homePhone"
                                class="form-control @error('homePhone') !border-danger-500 @enderror">
                            @error('homePhone')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div class="form-group">
                            <label for="cityId" class="form-label">City <span class="text-danger-500">*</span></label>
                            <select id="cityId" wire:model.live="cityId"
                                class="form-control @error('cityId') !border-danger-500 @enderror">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('cityId')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="areaId" class="form-label">Area <span class="text-danger-500">*</span></label>
                            <select id="areaId" wire:model="areaId"
                                class="form-control @error('areaId') !border-danger-500 @enderror">
                                <option value="">
                                    @if ($cityId)
                                        Select Area
                                    @else
                                        Please select a city
                                    @endif
                                </option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('areaId')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="form-group">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" id="nationality" wire:model="nationality"
                                class="form-control @error('nationality') !border-danger-500 @enderror">
                            @error('nationality')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="form-group col-span-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" wire:model="address" class="form-control @error('address') !border-danger-500 @enderror"
                                rows="2"></textarea>
                            @error('address')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="form-group">
                            <label for="birthDate" class="form-label">Birth Date</label>
                            <input type="date" id="birthDate" wire:model="birthDate"
                                class="form-control @error('birthDate') !border-danger-500 @enderror">
                            @error('birthDate')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" wire:model="gender"
                                class="form-control @error('gender') !border-danger-500 @enderror">
                                <option value="">Select Gender</option>
                                @foreach ($genderOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div class="form-group">
                            <label for="maritalStatus" class="form-label">Marital Status</label>
                            <select id="maritalStatus" wire:model="maritalStatus"
                                class="form-control @error('maritalStatus') !border-danger-500 @enderror">
                                <option value="">Select Marital Status</option>
                                @foreach ($maritalStatusOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('maritalStatus')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Military Status -->
                        <div class="form-group">
                            <label for="militaryStatus" class="form-label">Military Status</label>
                            <select id="militaryStatus" wire:model="militaryStatus"
                                class="form-control @error('militaryStatus') !border-danger-500 @enderror">
                                <option value="">Select Military Status</option>
                                @foreach ($militaryStatusOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('militaryStatus')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Social Number -->
                        <div class="form-group">
                            <label for="socialNumber" class="form-label">Social Number</label>
                            <input type="text" id="socialNumber" wire:model="socialNumber"
                                class="form-control @error('socialNumber') !border-danger-500 @enderror">
                            @error('socialNumber')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Channel -->
                        <div class="form-group">
                            <label for="channelId" class="form-label">Application Channel</label>
                            <select id="channelId" wire:model="channelId"
                                class="form-control @error('channelId') !border-danger-500 @enderror">
                                <option value="">Select Channel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                            @error('channelId')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Profile Image -->
                        <div class="form-group">
                            <label for="profileImage" class="form-label">Profile Image</label>
                            <input type="file" id="profileImage" wire:model="profileImage"
                                class="form-control @error('profileImage') !border-danger-500 @enderror">
                            <div wire:loading wire:target="profileImage" class="text-primary-500 text-sm mt-2">
                                Uploading...</div>
                            @error('profileImage')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                            @if ($profileImage)
                                <div class="mt-2">
                                    <img src="{{ $profileImage->temporaryUrl() }}"
                                        class="h-20 w-20 object-cover rounded-md">
                                </div>
                            @endif
                        </div>

                        <!-- CV -->
                        <div class="form-group">
                            <label for="cv" class="form-label">CV/Resume</label>
                            <input type="file" id="cv" wire:model="cv"
                                class="form-control @error('cv') !border-danger-500 @enderror">
                            <div wire:loading wire:target="cv" class="text-primary-500 text-sm mt-2">Uploading...
                            </div>
                            @error('cv')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                            @if ($cv)
                                <div class="mt-2">
                                    <span class="text-success-500">File selected</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Step 2: Education -->
                @if ($currentStep === 2)
                    <h4 class="text-xl font-medium mb-5">Education Information</h4>

                    @foreach ($educations as $index => $education)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">Education #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeEducation({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- School Name -->
                                <div class="form-group">
                                    <label class="form-label">School/University <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.school_name"
                                        class="form-control @error('educations.' . $index . '.school_name') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.school_name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Degree -->
                                <div class="form-group">
                                    <label class="form-label">Degree <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.degree"
                                        class="form-control @error('educations.' . $index . '.degree') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.degree')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Field of Study -->
                                <div class="form-group">
                                    <label class="form-label">Field of Study <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.field_of_study"
                                        class="form-control @error('educations.' . $index . '.field_of_study') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.field_of_study')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">Start Date <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="educations.{{ $index }}.start_date"
                                        class="form-control @error('educations.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.start_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="date" wire:model="educations.{{ $index }}.end_date"
                                        class="form-control @error('educations.' . $index . '.end_date') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.end_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addEducation" class="btn btn-outline-primary mt-2">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        Add Education
                    </button>
                @endif

                <!-- Step 3: Training -->
                @if ($currentStep === 3)
                    <h4 class="text-xl font-medium mb-5">Training Information</h4>

                    @foreach ($trainings as $index => $training)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">Training #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeTraining({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Training Name -->
                                <div class="form-group">
                                    <label class="form-label">Training Name <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.name"
                                        class="form-control @error('trainings.' . $index . '.name') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Sponsor -->
                                <div class="form-group">
                                    <label class="form-label">Sponsor/Organization <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.sponsor"
                                        class="form-control @error('trainings.' . $index . '.sponsor') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.sponsor')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="form-group">
                                    <label class="form-label">Duration <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.duration"
                                        class="form-control @error('trainings.' . $index . '.duration') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.duration')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">Start Date <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="trainings.{{ $index }}.start_date"
                                        class="form-control @error('trainings.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.start_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addTraining" class="btn btn-outline-primary mt-2">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        Add Training
                    </button>
                @endif

                <!-- Step 4: Experience -->
                @if ($currentStep === 4)
                    <h4 class="text-xl font-medium mb-5">Work Experience</h4>

                    @foreach ($experiences as $index => $experience)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">Experience #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeExperience({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Company Name -->
                                <div class="form-group">
                                    <label class="form-label">Company Name <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="experiences.{{ $index }}.company_name"
                                        class="form-control @error('experiences.' . $index . '.company_name') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.company_name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Position -->
                                <div class="form-group">
                                    <label class="form-label">Position <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="experiences.{{ $index }}.position"
                                        class="form-control @error('experiences.' . $index . '.position') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.position')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">Start Date <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="experiences.{{ $index }}.start_date"
                                        class="form-control @error('experiences.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.start_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="form-group">
                                    <label class="form-label">End Date</label>
                                    <input type="date" wire:model="experiences.{{ $index }}.end_date"
                                        class="form-control @error('experiences.' . $index . '.end_date') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.end_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Salary -->
                                <div class="form-group">
                                    <label class="form-label">Salary</label>
                                    <input type="text" wire:model="experiences.{{ $index }}.salary"
                                        class="form-control @error('experiences.' . $index . '.salary') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.salary')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Reason for Leaving -->
                                <div class="form-group">
                                    <label class="form-label">Reason for Leaving</label>
                                    <input type="text"
                                        wire:model="experiences.{{ $index }}.reason_for_leaving"
                                        class="form-control @error('experiences.' . $index . '.reason_for_leaving') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.reason_for_leaving')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addExperience" class="btn btn-outline-primary mt-2">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        Add Experience
                    </button>
                @endif

                <!-- Step 5: Languages -->
                @if ($currentStep === 5)
                    <h4 class="text-xl font-medium mb-5">Language Skills</h4>

                    @foreach ($languages as $index => $language)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">Language #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeLanguage({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Language -->
                                <div class="form-group">
                                    <label class="form-label">Language <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="languages.{{ $index }}.language"
                                        class="form-control @error('languages.' . $index . '.language') !border-danger-500 @enderror">
                                    @error('languages.' . $index . '.language')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Speaking Level -->
                                <div class="form-group">
                                    <label class="form-label">Speaking Level</label>
                                    <select wire:model="languages.{{ $index }}.speaking_level"
                                        class="form-control @error('languages.' . $index . '.speaking_level') !border-danger-500 @enderror">
                                        <option value="">Select Speaking Level</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ ucwords(str_replace('_', ' ', $level)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.speaking_level')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Writing Level -->
                                <div class="form-group">
                                    <label class="form-label">Writing Level</label>
                                    <select wire:model="languages.{{ $index }}.writing_level"
                                        class="form-control @error('languages.' . $index . '.writing_level') !border-danger-500 @enderror">
                                        <option value="">Select Writing Level</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ ucwords(str_replace('_', ' ', $level)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.writing_level')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Reading Level -->
                                <div class="form-group">
                                    <label class="form-label">Reading Level</label>
                                    <select wire:model="languages.{{ $index }}.reading_level"
                                        class="form-control @error('languages.' . $index . '.reading_level') !border-danger-500 @enderror">
                                        <option value="">Select Reading Level</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ ucwords(str_replace('_', ' ', $level)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.reading_level')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addLanguage" class="btn btn-outline-primary mt-2">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        Add Language
                    </button>
                @endif

                <!-- Step 6: References -->
                @if ($currentStep === 6)
                    <h4 class="text-xl font-medium mb-5">References</h4>

                    @foreach ($references as $index => $reference)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">Reference #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeReference({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Reference Name -->
                                <div class="form-group">
                                    <label class="form-label">Name <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="references.{{ $index }}.name"
                                        class="form-control @error('references.' . $index . '.name') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.name')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="form-group">
                                    <label class="form-label">Phone <span class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="references.{{ $index }}.phone"
                                        class="form-control @error('references.' . $index . '.phone') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.phone')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" wire:model="references.{{ $index }}.email"
                                        class="form-control @error('references.' . $index . '.email') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.email')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" wire:model="references.{{ $index }}.address"
                                        class="form-control @error('references.' . $index . '.address') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.address')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Relationship -->
                                <div class="form-group">
                                    <label class="form-label">Relationship</label>
                                    <input type="text" wire:model="references.{{ $index }}.relationship"
                                        class="form-control @error('references.' . $index . '.relationship') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.relationship')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addReference" class="btn btn-outline-primary mt-2">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        Add Reference
                    </button>
                @endif

                <!-- Step 7: Skills & Health -->
                @if ($currentStep === 7)
                    <h4 class="text-xl font-medium mb-5">Skills & Health Information</h4>

                    <!-- Skills Section -->
                    <div class="mb-6">
                        <h5 class="text-lg font-medium mb-3">Skills</h5>

                        @foreach ($skills as $index => $skill)
                            <div class="border p-4 rounded-md mb-4 bg-slate-50">
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="font-medium">Skill #{{ $index + 1 }}</h5>
                                    <button type="button" wire:click="removeSkill({{ $index }})"
                                        class="text-danger-500">
                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Skill Type -->
                                    <div class="form-group">
                                        <label class="form-label">Skill Type <span
                                                class="text-danger-500">*</span></label>
                                        <select wire:model.live="skills.{{ $index }}.type"
                                            class="form-control @error('skills.' . $index . '.type') !border-danger-500 @enderror">
                                            <option value="">Select Skill Type</option>
                                            <option value="computer">Computer Skill</option>
                                            <option value="technical">Technical Skill</option>
                                            <option value="soft">Soft Skill</option>
                                            <option value="other">Other</option>
                                        </select>
                                        @error('skills.' . $index . '.type')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Skill -->
                                    <div class="form-group">
                                        <label class="form-label">Skill <span class="text-danger-500">*</span></label>
                                        <input type="text" wire:model="skills.{{ $index }}.skill"
                                            class="form-control @error('skills.' . $index . '.skill') !border-danger-500 @enderror"
                                            list="skillOptions{{ $index }}">

                                        @if ($skills[$index]['type'] === 'computer')
                                            <datalist id="skillOptions{{ $index }}">
                                                @foreach ($computerSkillsList as $option)
                                                    <option value="{{ $option }}">
                                                @endforeach
                                            </datalist>
                                        @elseif ($skills[$index]['type'] === 'technical')
                                            <datalist id="skillOptions{{ $index }}">
                                                @foreach ($technicalSkillsList as $option)
                                                    <option value="{{ $option }}">
                                                @endforeach
                                            </datalist>
                                        @elseif ($skills[$index]['type'] === 'soft')
                                            <datalist id="skillOptions{{ $index }}">
                                                @foreach ($softSkillsList as $option)
                                                    <option value="{{ $option }}">
                                                @endforeach
                                            </datalist>
                                        @endif

                                        @error('skills.' . $index . '.skill')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Skill Level -->
                                    <div class="form-group">
                                        <label class="form-label">Proficiency Level <span
                                                class="text-danger-500">*</span></label>
                                        <select wire:model="skills.{{ $index }}.level"
                                            class="form-control @error('skills.' . $index . '.level') !border-danger-500 @enderror">
                                            <option value="">Select Level</option>
                                            @foreach ($skillLevels as $level)
                                                <option value="{{ $level }}">{{ $level }}</option>
                                            @endforeach
                                        </select>
                                        @error('skills.' . $index . '.level')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addSkill" class="btn btn-outline-primary mt-2">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                            Add Skill
                        </button>
                    </div>

                    <!-- Health Section -->
                    <div>
                        <h5 class="text-lg font-medium mb-3">Health Information</h5>

                        <div class="form-group mb-4">
                            <label class="form-label">Do you have any health conditions that might affect your
                                work?</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="hasHealthIssues" value="1"
                                        class="form-radio">
                                    <span class="text-sm font-medium text-slate-600 ml-2">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="hasHealthIssues" value="0"
                                        class="form-radio">
                                    <span class="text-sm font-medium text-slate-600 ml-2">No</span>
                                </label>
                            </div>
                            @error('hasHealthIssues')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($hasHealthIssues)
                            <div class="form-group mb-4">
                                <label class="form-label">Please describe your health conditions</label>
                                <textarea wire:model="healthConditionsDescription"
                                    class="form-control @error('healthConditionsDescription') !border-danger-500 @enderror" rows="3"></textarea>
                                @error('healthConditionsDescription')
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                    </div>
                @endif

                <!-- Step 8: Vacancy & Application -->
                @if ($currentStep === 8)
                    <h4 class="text-xl font-medium mb-5">Vacancy & Application Details</h4>

                    @if ($selectedVacancy)
                        <!-- Vacancy Information -->

                        <div class="flex justify-between">

                            <div class="w-full p-4 bg-slate-50 rounded-md mb-6">
                                <h5 class="font-medium mb-2">Vacancy Details:</h5>
                                <p><strong>Position:</strong> {{ $selectedVacancy?->position?->name }}</p>
                                <p><strong>Department:</strong>
                                    {{ $selectedVacancy?->position?->department?->name }}</p>
                                <p><strong>Opening Date:</strong> {{ $selectedVacancy?->created_at->format('d M Y') }}
                                </p>
                                <p><strong>Closing Date:</strong>
                                    {{ $selectedVacancy?->closing_date->format('d M Y') }}
                                </p>
                                <p><strong>Status:</strong> <span
                                        class="badge {{ $selectedVacancy?->status === 'open' ? 'bg-success-500' : 'bg-danger-500' }}">{{ ucfirst($selectedVacancy?->status) }}</span>
                                </p>
                            </div>
                            @if ($canEditVacancy)
                                <div class="flex items-center p-4 bg-slate-50 rounded-md mb-6">
                                    <button type="button" wire:click="clearSelectedVacancy" class="text-danger-500">
                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Pick Preferred Interview Slot -->
                        @if ($selectedVacancy->vacancy_slots->count() > 0)
                            <div class="mb-6">
                                <h5 class="font-medium mb-4">Preferred Interview Slot</h5>

                                <x-select wire:model="slotId">
                                    <option value="">All slots are Ok</option>
                                    @foreach ($selectedVacancy->vacancy_slots as $slot)
                                        <option value="{{ $slot->id }}">
                                            {{ $slot->date->format('d M Y') }} -
                                            {{ $slot->start_time->format('H:i') }} to
                                            {{ $slot->end_time->format('H:i') }}
                                        </option>
                                    @endforeach
                                </x-select>

                            </div>
                        @endif

                        <!-- Base Questions (if any) -->
                        @if (count($allVacancyQuestions) > 0)
                            <div class="mb-6">
                                <h5 class="font-medium mb-4">Application Questions</h5>
                                @foreach ($allVacancyQuestions as $index => $question)
                                    <div class="mb-4 p-4 border rounded-md">
                                        <p class="font-medium mb-2">{{ $index + 1 }}.
                                            {{ $question['question'] }}
                                            @if ($question['required'])
                                                <span class="text-danger-500">*</span>
                                            @endif
                                        </p>

                                        @if ($question['type'] === 'textarea')
                                            <div class="form-group">
                                                <textarea wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror" rows="3"></textarea>
                                            </div>
                                        @elseif ($question['type'] === 'radio')
                                            <div class="space-y-2">
                                                @foreach ($question['options_array'] as $optionIndex => $option)
                                                    <label class="flex items-center">
                                                        <input type="radio"
                                                            wire:model="questionAnswers.{{ $index }}.answer"
                                                            value="{{ $option }}" class="form-radio"
                                                            name="radio{{ $optionIndex }}">
                                                        <span
                                                            class="text-sm font-medium text-slate-600 ml-2 @error('questionAnswers.' . $question['id'] . '.answer') !border-danger-500 @enderror ">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif ($question['type'] === 'checkbox')
                                            <div class="space-y-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox"
                                                        wire:model="questionAnswers.{{ $index }}.answer"
                                                        value="true" class="form-checkbox">
                                                    <span class="text-sm font-medium text-slate-600 ml-2">True</span>
                                                </label>
                                            </div>
                                        @elseif ($question['type'] === 'select')
                                            <div class="form-group">
                                                <select wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                                    <option value="">Select an option</option>
                                                    @foreach ($question['options_array'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif ($question['type'] === 'date')
                                            <div class="form-group">
                                                <input type="date"
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @elseif($question['type'] === 'number')
                                            <div class="form-group">
                                                <input type="number"
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <input type="text"
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @endif
                                        @error('questionAnswers.' . $index . '.answer')
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- Vacancy Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label">Select Vacancy <span class="text-danger-500">*</span></label>
                            <select wire:model.live="vacancyId"
                                class="form-control @error('vacancyId') !border-danger-500 @enderror">
                                <option value="">Select Vacancy</option>
                                @foreach ($vacancies as $vacancy)
                                    <option value="{{ $vacancy->id }}">{{ $vacancy->title }}
                                        ({{ $vacancy->position->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vacancyId')
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Cover Letter -->
                    <div class="form-group mb-4">
                        <label class="form-label">Cover Letter (Optional)</label>
                        <textarea wire:model="coverLetter" class="form-control @error('coverLetter') !border-danger-500 @enderror"
                            rows="3" placeholder="Do you want to send a cover letter to the recruiter?"></textarea>
                        @error('coverLetter')
                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-group mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="agreeToTerms" class="form-checkbox">
                            <span class="text-sm font-medium text-slate-600 ml-2">
                                I agree to the <a href="#" class="text-primary-500">Terms and Conditions</a> and
                                confirm that all information provided is accurate.
                            </span>
                        </label>
                        @error('agreeToTerms')
                            <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Step navigation -->
                <div class="flex justify-between mt-10">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="previousStep" class="btn btn-outline-primary">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:arrow-left-bold"></iconify-icon>
                            Previous
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="btn btn-primary flex items-center">
                            <span>Next</span>
                            <iconify-icon class="text-sm ltr:ml-2 rtl:mr-2" icon="ph:arrow-right-bold"></iconify-icon>
                        </button>
                    @else
                        <button type="button" wire:click="createApplicant" class="btn btn-success"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createApplicant">
                                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:check-bold"></iconify-icon>
                                Submit Application
                            </span>
                            <span wire:loading wire:target="createApplicant">
                                <div class="flex items-center">
                                    <svg class="animate-spin ltr:-ml-1 ltr:mr-2 rtl:-mr-1 rtl:ml-2 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Processing...
                                </div>
                            </span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>


    <style>
        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-item:not(:last-child):after {
            content: '';
            position: absolute;
            top: 20px;
            width: 100%;
            height: 2px;
            background-color: #e5e7eb;
            z-index: 0;
        }

        .step-item.completed:not(:last-child):after {
            background-color: #3b82f6;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 8px;
            position: relative;
            z-index: 1;
        }

        .step-item.active .step-number,
        .step-item.completed .step-number {
            background-color: #3b82f6;
            color: white;
        }

        .step-title {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .step-item.active .step-title,
        .step-item.completed .step-title {
            color: #1f2937;
            font-weight: 500;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('stepChanged', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    @endpush

</div>
