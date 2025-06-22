<div class="{{ $locale === 'ar' ? 'rtl arabic-font text-right' : 'ltr text-left' }}">
    <div class="flex justify-between flex-wrap items-center mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
            {{ $locale === 'ar' ? 'إنشاء متقدم جديد' : 'Create New Applicant' }}
        </h4>

        <!-- Language Switcher -->
        <div class="flex items-center {{ $locale === 'ar' ? 'space-x-reverse' : 'space-x-2' }}">
            <span class="text-sm font-medium">{{ __('recruitment.switch_language') }}:</span>
            <button wire:click="switchLocale('en')"
                class="px-3 py-1 rounded-md {{ $locale === 'en' ? 'bg-primary-500 text-white' : 'bg-slate-100 hover:bg-slate-200' }}"
                type="button">
                {{ __('recruitment.english') }}
            </button>
            <button wire:click="switchLocale('ar')"
                class="px-3 py-1 rounded-md {{ $locale === 'ar' ? 'bg-primary-500 text-white' : 'bg-slate-100 hover:bg-slate-200' }}"
                type="button">
                {{ __('recruitment.arabic') }}
            </button>
        </div>
    </div>

    <div class="card">
        <!-- Progress Steps -->
        <div class="p-5">
            <div class="flex justify-between mb-3 {{ $locale === 'ar' ? 'flex-row-reverse' : '' }}">
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <div
                        class="step-item {{ $currentStep >= $i ? 'active' : '' }} {{ $currentStep > $i ? 'completed' : '' }}">
                        <div class="step-number">{{ $i }}</div>
                        <div class="step-title hidden md:block">
                            @switch($i)
                                @case(1)
                                    {{ __('recruitment.personal_information') }}
                                @break

                                @case(2)
                                    {{ __('recruitment.education') }}
                                @break

                                @case(3)
                                    {{ __('recruitment.training_information') }}
                                @break

                                @case(4)
                                    {{ __('recruitment.experience') }}
                                @break

                                @case(5)
                                    {{ __('recruitment.languages') }}
                                @break

                                @case(6)
                                    {{ __('recruitment.references') }}
                                @break

                                @case(7)
                                    {{ __('recruitment.skills_health') }}
                                @break

                                @case(8)
                                    {{ __('recruitment.vacancy') }}
                                @break
                            @endswitch
                        </div>

                    </div>
                @endfor
            </div>
        </div>

        <!-- Form Content -->
        <div class="card-body px-6 pb-6">
            <form wire:submit.prevent="{{ $currentStep < $totalSteps ? 'nextStep' : 'createApplicant' }}">
                <!-- Step 1: Personal Information -->
                @if ($currentStep === 1)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.personal_information') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full"
                        @if ($locale === 'ar') dir="rtl" @endif>
                        <!-- First Name -->
                        <div class="form-group sm:col-span-3">
                            <label for="firstName" class="form-label">{{ __('recruitment.first_name') }} <span
                                    class="text-danger-500">*</span></label>
                            <input type="text" id="firstName" wire:model="firstName"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('firstName') !border-danger-500 @enderror">
                            @error('firstName')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>
                        <!-- Middle Name -->
                        <div class="form-group sm:col-span-3">
                            <label for="middleName" class="form-label">{{ __('recruitment.middle_name') }}</label>
                            <input type="text" id="middleName" wire:model="middleName"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('middleName') !border-danger-500 @enderror">
                            @error('middleName')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>
                        <!-- Last Name -->
                        <div class="form-group sm:col-span-3">
                            <label for="lastName" class="form-label">{{ __('recruitment.last_name') }} <span
                                    class="text-danger-500">*</span></label>
                            <input type="text" id="lastName" wire:model="lastName"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('lastName') !border-danger-500 @enderror">
                            @error('lastName')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group sm:col-span-3">
                            <label for="email" class="form-label">{{ __('recruitment.email') }} <span
                                    class="text-danger-500">*</span></label>
                            <input type="email" id="email" wire:model="email"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('email') !border-danger-500 @enderror">
                            @error('email')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group sm:col-span-3">
                            <label for="phone" class="form-label">{{ __('recruitment.phone') }} <span
                                    class="text-danger-500">*</span></label>
                            <input type="text" id="phone" wire:model="phone"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('phone') !border-danger-500 @enderror">
                            @error('phone')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Home Phone -->
                        <div class="form-group sm:col-span-3">
                            <label for="homePhone" class="form-label">{{ __('recruitment.home_phone') }}</label>
                            <input type="text" id="homePhone" wire:model="homePhone"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('homePhone') !border-danger-500 @enderror">
                            @error('homePhone')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="form-group sm:col-span-3">
                            <label for="cityId" class="form-label">{{ __('recruitment.city') }} <span
                                    class="text-danger-500">*</span></label>
                            <select id="cityId" wire:model.live="cityId"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('cityId') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_city') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('cityId')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div class="form-group sm:col-span-3">
                            <label for="areaId" class="form-label">{{ __('recruitment.area') }} <span
                                    class="text-danger-500">*</span></label>
                            <select id="areaId" wire:model="areaId"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('areaId') !border-danger-500 @enderror">
                                <option value="">
                                    @if ($cityId)
                                        {{ __('recruitment.select_area') }}
                                    @else
                                        {{ __('recruitment.please_select_city') }}
                                    @endif
                                </option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            @error('areaId')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="form-group sm:col-span-3">
                            <label for="nationality" class="form-label">{{ __('recruitment.nationality') }}</label>
                            <input type="text" id="nationality" wire:model="nationality"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('nationality') !border-danger-500 @enderror">
                            @error('nationality')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="form-group sm:col-span-3">
                            <label for="address" class="form-label">{{ __('recruitment.address') }}</label>
                            <textarea id="address" wire:model="address" @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('address') !border-danger-500 @enderror" rows="2"></textarea>
                            @error('address')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="form-group sm:col-span-3">
                            <label for="birthDate" class="form-label">{{ __('recruitment.birth_date') }} <span
                                    class="text-danger-500">*</span></label>
                            <input type="date" id="birthDate" wire:model="birthDate"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('birthDate') !border-danger-500 @enderror">
                            @error('birthDate')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-group sm:col-span-3">
                            <label for="gender" class="form-label">{{ __('recruitment.gender') }} <span
                                class="text-danger-500">*</span></label>
                            <select id="gender" wire:model="gender"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('gender') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_gender') }}</option>
                                @foreach ($genderOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div class="form-group sm:col-span-3">
                            <label for="maritalStatus"
                                class="form-label">{{ __('recruitment.marital_status') }} <span
                                    class="text-danger-500">*</span></label>
                            <select id="maritalStatus" wire:model="maritalStatus"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('maritalStatus') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_marital_status') }}</option>
                                @foreach ($maritalStatusOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('maritalStatus')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Military Status -->
                        <div class="form-group sm:col-span-3">
                            <label for="militaryStatus"
                                class="form-label">{{ __('recruitment.military_status') }} <span
                                    class="text-danger-500">*</span></label>
                            <select id="militaryStatus" wire:model="militaryStatus"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('militaryStatus') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_military_status') }}</option>
                                @foreach ($militaryStatusOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('militaryStatus')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Social Number -->
                        <div class="form-group sm:col-span-3">
                            <label for="socialNumber" class="form-label w-full">{{ __('recruitment.social_number') }}
                                <span class="text-danger-500">*</span>
                            </label>
                            <input type="text" id="socialNumber" wire:model="socialNumber"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('socialNumber') !border-danger-500 @enderror">
                            @error('socialNumber')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        <!-- Channel -->
                        <div class="form-group sm:col-span-3">
                            <label for="channelId"
                                class="form-label">{{ __('recruitment.application_channel') }}</label>
                            <select id="channelId" wire:model="channelId"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('channelId') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_channel') }}</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                            @error('channelId')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Profile Image -->
                        <div class="form-group">
                            <label for="profileImage"
                                class="form-label">{{ __('recruitment.profile_image') }}</label>
                            <input type="file" id="profileImage" wire:model="profileImage"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('profileImage') !border-danger-500 @enderror">
                            <div wire:loading wire:target="profileImage" class="text-primary-500 text-sm mt-2">
                                {{ __('recruitment.uploading') }}</div>
                            @error('profileImage')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
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
                            <label for="cv" class="form-label">{{ __('recruitment.cv_resume') }}</label>
                            <input type="file" id="cv" wire:model="cv"
                                @if ($locale === 'ar') dir="rtl" @endif
                                class="form-control @error('cv') !border-danger-500 @enderror">
                            <div wire:loading wire:target="cv" class="text-primary-500 text-sm mt-2">
                                {{ __('recruitment.uploading') }}
                            </div>
                            @error('cv')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                            @if ($cv)
                                <div class="mt-2">
                                    <span class="text-success-500">{{ __('recruitment.file_selected') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Documents -->
                    <div class="mt-6">
                        <h5 class="text-lg font-medium mb-4">{{ __('recruitment.additional_documents') }}</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ID Card -->
                            <div class="form-group">
                                <label for="idCard" class="form-label">{{ __('recruitment.id_card') }}</label>
                                <input type="file" id="idCard" wire:model="idCard"
                                    @if ($locale === 'ar') dir="rtl" @endif
                                    class="form-control @error('idCard') !border-danger-500 @enderror">
                                <div wire:loading wire:target="idCard" class="text-primary-500 text-sm mt-2">
                                    {{ __('recruitment.uploading') }}
                                </div>
                                @error('idCard')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                                @if ($idCard)
                                    <div class="mt-2">
                                        <span class="text-success-500">{{ __('recruitment.file_selected') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Birth Certificate -->
                            <div class="form-group">
                                <label for="birthCertificate" class="form-label">{{ __('recruitment.birth_certificate') }}</label>
                                <input type="file" id="birthCertificate" wire:model="birthCertificate"
                                    @if ($locale === 'ar') dir="rtl" @endif
                                    class="form-control @error('birthCertificate') !border-danger-500 @enderror">
                                <div wire:loading wire:target="birthCertificate" class="text-primary-500 text-sm mt-2">
                                    {{ __('recruitment.uploading') }}
                                </div>
                                @error('birthCertificate')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                                @if ($birthCertificate)
                                    <div class="mt-2">
                                        <span class="text-success-500">{{ __('recruitment.file_selected') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- College Certificate -->
                            <div class="form-group">
                                <label for="collegeCertificate" class="form-label">{{ __('recruitment.college_certificate') }}</label>
                                <input type="file" id="collegeCertificate" wire:model="collegeCertificate"
                                    @if ($locale === 'ar') dir="rtl" @endif
                                    class="form-control @error('collegeCertificate') !border-danger-500 @enderror">
                                <div wire:loading wire:target="collegeCertificate" class="text-primary-500 text-sm mt-2">
                                    {{ __('recruitment.uploading') }}
                                </div>
                                @error('collegeCertificate')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                                @if ($collegeCertificate)
                                    <div class="mt-2">
                                        <span class="text-success-500">{{ __('recruitment.file_selected') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Army Certificate -->
                            <div class="form-group">
                                <label for="armyCertificate" class="form-label">{{ __('recruitment.army_certificate') }}</label>
                                <input type="file" id="armyCertificate" wire:model="armyCertificate"
                                    @if ($locale === 'ar') dir="rtl" @endif
                                    class="form-control @error('armyCertificate') !border-danger-500 @enderror">
                                <div wire:loading wire:target="armyCertificate" class="text-primary-500 text-sm mt-2">
                                    {{ __('recruitment.uploading') }}
                                </div>
                                @error('armyCertificate')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                                @if ($armyCertificate)
                                    <div class="mt-2">
                                        <span class="text-success-500">{{ __('recruitment.file_selected') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Step 2: Education -->
                @if ($currentStep === 2)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.education') }}</h4>

                    @foreach ($educations as $index => $education)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">{{ __('recruitment.education') }} #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeEducation({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- School Name -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.school_university') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.school_name"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('educations.' . $index . '.school_name') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.school_name')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Degree -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.degree') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.degree" list="degrees"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('educations.' . $index . '.degree') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.degree')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                    <datalist id="degrees">
                                        @foreach ($degrees as $degree)
                                            <option value="{{ $degree }}">{{ $degree }}</option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Field of Study -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.field_of_study') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="educations.{{ $index }}.field_of_study"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('educations.' . $index . '.field_of_study') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.field_of_study')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.start_date') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="educations.{{ $index }}.start_date"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('educations.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.start_date')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.end_date') }}</label>
                                    <input type="date" wire:model="educations.{{ $index }}.end_date"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('educations.' . $index . '.end_date') !border-danger-500 @enderror">
                                    @error('educations.' . $index . '.end_date')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addEducation"
                        class="btn btn-dark flex items-center btn-sm mt-2">
                        <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        {{ __('recruitment.add_education') }}
                    </button>
                @endif

                <!-- Step 3: Training -->
                @if ($currentStep === 3)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.training_information') }}</h4>

                    @foreach ($trainings as $index => $training)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">{{ __('recruitment.training') }} #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeTraining({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Training Name -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.training_name') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.name"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('trainings.' . $index . '.name') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.name')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Sponsor -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.sponsor') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.sponsor"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('trainings.' . $index . '.sponsor') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.sponsor')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.duration') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="trainings.{{ $index }}.duration"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('trainings.' . $index . '.duration') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.duration')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.start_date') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="trainings.{{ $index }}.start_date"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('trainings.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('trainings.' . $index . '.start_date')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addTraining"
                        class="btn btn-dark flex items-center btn-sm mt-2">
                        <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        {{ __('recruitment.add_training') }}
                    </button>
                @endif

                <!-- Step 4: Experience -->
                @if ($currentStep === 4)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.work_experience') }}</h4>

                    @foreach ($experiences as $index => $experience)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">{{ __('recruitment.experience') }} #{{ $index + 1 }}
                                </h5>
                                <button type="button" wire:click="removeExperience({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Company Name -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.company_name') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="experiences.{{ $index }}.company_name"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.company_name') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.company_name')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Position -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.position') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="experiences.{{ $index }}.position"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.position') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.position')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.start_date') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="date" wire:model="experiences.{{ $index }}.start_date"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.start_date') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.start_date')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.end_date') }}</label>
                                    <input type="date" wire:model="experiences.{{ $index }}.end_date"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.end_date') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.end_date')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Salary -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.salary') }}</label>
                                    <input type="text" wire:model="experiences.{{ $index }}.salary"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.salary') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.salary')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Reason for Leaving -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.reason_for_leaving') }}</label>
                                    <input type="text"
                                        wire:model="experiences.{{ $index }}.reason_for_leaving"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('experiences.' . $index . '.reason_for_leaving') !border-danger-500 @enderror">
                                    @error('experiences.' . $index . '.reason_for_leaving')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addExperience"
                        class="btn btn-dark flex items-center btn-sm mt-2">
                        <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        {{ __('recruitment.add_experience') }}
                    </button>
                @endif

                <!-- Step 5: Languages -->
                @if ($currentStep === 5)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.language_skills') }}</h4>

                    @foreach ($languages as $index => $language)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">{{ __('recruitment.language') }} #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeLanguage({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Language -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.language') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="languages.{{ $index }}.language" list="languages"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('languages.' . $index . '.language') !border-danger-500 @enderror">
                                    @error('languages.' . $index . '.language')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                    <datalist id="languages">
                                        @foreach ($baseLanguages as $l)
                                            <option value="{{ $l }}">{{ $l }}</option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Speaking Level -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.speaking_level') }}</label>
                                    <select wire:model="languages.{{ $index }}.speaking_level"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('languages.' . $index . '.speaking_level') !border-danger-500 @enderror">
                                        <option value="">{{ __('recruitment.select_level') }}</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ __('recruitment.' . $level) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.speaking_level')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Writing Level -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.writing_level') }}</label>
                                    <select wire:model="languages.{{ $index }}.writing_level"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('languages.' . $index . '.writing_level') !border-danger-500 @enderror">
                                        <option value="">{{ __('recruitment.select_level') }}</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ __('recruitment.' . $level) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.writing_level')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Reading Level -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.reading_level') }}</label>
                                    <select wire:model="languages.{{ $index }}.reading_level"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('languages.' . $index . '.reading_level') !border-danger-500 @enderror">
                                        <option value="">{{ __('recruitment.select_level') }}</option>
                                        @foreach ($proficiencyLevels as $level)
                                            <option value="{{ $level }}">
                                                {{ __('recruitment.' . $level) }}</option>
                                        @endforeach
                                    </select>
                                    @error('languages.' . $index . '.reading_level')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addLanguage"
                        class="btn btn-dark flex items-center btn-sm mt-2">
                        <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        {{ __('recruitment.add_language') }}
                    </button>
                @endif

                <!-- Step 6: References -->
                @if ($currentStep === 6)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.references') }}</h4>

                    @foreach ($references as $index => $reference)
                        <div class="border p-4 rounded-md mb-4 bg-slate-50">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium">{{ __('recruitment.reference') }} #{{ $index + 1 }}</h5>
                                <button type="button" wire:click="removeReference({{ $index }})"
                                    class="text-danger-500">
                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Reference Name -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.name') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="references.{{ $index }}.name"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('references.' . $index . '.name') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.name')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.phone') }} <span
                                            class="text-danger-500">*</span></label>
                                    <input type="text" wire:model="references.{{ $index }}.phone"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('references.' . $index . '.phone') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.phone')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.email') }}</label>
                                    <input type="email" wire:model="references.{{ $index }}.email"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('references.' . $index . '.email') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.email')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.address') }}</label>
                                    <input type="text" wire:model="references.{{ $index }}.address"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('references.' . $index . '.address') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.address')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>

                                <!-- Relationship -->
                                <div class="form-group">
                                    <label class="form-label">{{ __('recruitment.relationship') }}</label>
                                    <input type="text" wire:model="references.{{ $index }}.relationship"
                                        @if ($locale === 'ar') dir="rtl" @endif
                                        class="form-control @error('references.' . $index . '.relationship') !border-danger-500 @enderror">
                                    @error('references.' . $index . '.relationship')
                                        <bdi>
                                            <span
                                                class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                        </bdi>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addReference"
                        class="btn btn-dark flex items-center btn-sm mt-2">
                        <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                        {{ __('recruitment.add_reference') }}
                    </button>
                @endif

                <!-- Step 7: Skills & Health -->
                @if ($currentStep === 7)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.skills_health') }}</h4>

                    <!-- Skills Section -->
                    <div class="mb-6">
                        <h5 class="text-lg font-medium mb-3">{{ __('recruitment.skills') }}</h5>

                        @foreach ($skills as $index => $skill)
                            <div class="border p-4 rounded-md mb-4 bg-slate-50">
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="font-medium">{{ __('recruitment.skill') }} #{{ $index + 1 }}</h5>
                                    <button type="button" wire:click="removeSkill({{ $index }})"
                                        class="text-danger-500">
                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Skill Type -->
                                    <div class="form-group">
                                        <label class="form-label">{{ __('recruitment.skill_type') }} <span
                                                class="text-danger-500">*</span></label>
                                        <select wire:model.live="skills.{{ $index }}.type"
                                            @if ($locale === 'ar') dir="rtl" @endif
                                            class="form-control @error('skills.' . $index . '.type') !border-danger-500 @enderror">
                                            <option value="">{{ __('recruitment.select_skill_type') }}</option>
                                            <option value="computer">{{ __('recruitment.computer_skill') }}</option>
                                            <option value="technical">{{ __('recruitment.technical_skill') }}
                                            </option>
                                            <option value="soft">{{ __('recruitment.soft_skill') }}</option>
                                            <option value="other">{{ __('recruitment.other') }}</option>
                                        </select>
                                        @error('skills.' . $index . '.type')
                                            <bdi>
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            </bdi>
                                        @enderror
                                    </div>

                                    <!-- Skill -->
                                    <div class="form-group">
                                        <label class="form-label">{{ __('recruitment.skill') }} <span
                                                class="text-danger-500">*</span></label>
                                        <input type="text" wire:model="skills.{{ $index }}.skill"
                                            @if ($locale === 'ar') dir="rtl" @endif
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
                                            <bdi>
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            </bdi>
                                        @enderror
                                    </div>

                                    <!-- Skill Level -->
                                    <div class="form-group">
                                        <label class="form-label">{{ __('recruitment.skill_level') }} <span
                                                class="text-danger-500">*</span></label>
                                        <select wire:model="skills.{{ $index }}.level"
                                            @if ($locale === 'ar') dir="rtl" @endif
                                            class="form-control @error('skills.' . $index . '.level') !border-danger-500 @enderror">
                                            <option value="">{{ __('recruitment.select_level') }}</option>
                                            @foreach ($skillLevels as $level)
                                                <option value="{{ $level }}">
                                                    {{ __('recruitment.' . strtolower(str_replace(' ', '_', $level))) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('skills.' . $index . '.level')
                                            <bdi>
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            </bdi>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="addSkill"
                            class="btn btn-dark flex items-center btn-sm mt-2">
                            <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                            {{ __('recruitment.add_skill') }}
                        </button>
                    </div>

                    <!-- Health Section -->
                    <div>
                        <h5 class="text-lg font-medium mb-3">{{ __('recruitment.health_information') }}</h5>

                        <div class="form-group mb-4">
                            <label class="form-label">{{ __('recruitment.health_issues') }}</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="hasHealthIssues" value="1"
                                        @if ($locale === 'ar') dir="rtl" @endif class="form-radio">
                                    <span
                                        class="text-sm font-medium text-slate-600 ml-2">{{ __('recruitment.yes') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="hasHealthIssues" value="0"
                                        @if ($locale === 'ar') dir="rtl" @endif class="form-radio">
                                    <span
                                        class="text-sm font-medium text-slate-600 ml-2">{{ __('recruitment.no') }}</span>
                                </label>
                            </div>
                            @error('hasHealthIssues')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>

                        @if ($hasHealthIssues)
                            <div class="form-group mb-4">
                                <label class="form-label">{{ __('recruitment.describe_health_issues') }}</label>
                                <textarea wire:model="healthIssues" @if ($locale === 'ar') dir="rtl" @endif
                                    class="form-control @error('healthIssues') !border-danger-500 @enderror" rows="3"></textarea>
                                @error('healthIssues')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                            </div>
                        @endif

                    </div>
                @endif

                <!-- Step 8: Vacancy & Application -->
                @if ($currentStep === 8)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.vacancy_application_details') }}</h4>

                    @if ($selectedVacancy)
                        <!-- Vacancy Information -->

                        <div class="flex justify-between">

                            <div class="w-full p-4 bg-slate-50 rounded-md mb-6">
                                <h5 class="font-medium mb-2">{{ __('recruitment.vacancy_details') }}:</h5>
                                <p><strong>{{ __('recruitment.position') }}:</strong>
                                    {{ $selectedVacancy?->position?->name }}</p>
                                <p><strong>{{ __('recruitment.department') }}:</strong>
                                    {{ $selectedVacancy?->position?->department?->name }}</p>
                                <p><strong>{{ __('recruitment.opening_date') }}:</strong>
                                    {{ $selectedVacancy?->created_at->format('d M Y') }}
                                </p>
                                <p><strong>{{ __('recruitment.closing_date') }}:</strong>
                                    {{ $selectedVacancy?->closing_date }}
                                </p>
                                <p><strong>{{ __('recruitment.status') }}:</strong> <span
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
                                <h5 class="font-medium mb-4">{{ __('recruitment.preferred_interview_slot') }}</h5>

                                <x-select wire:model="slotId">
                                    <option value="">{{ __('recruitment.all_slots_ok') }}</option>
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
                                <h5 class="font-medium mb-4">{{ __('recruitment.application_questions') }}</h5>
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
                                                    @if ($locale === 'ar') dir="rtl" @endif
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror"
                                                    rows="3"></textarea>
                                            </div>
                                        @elseif ($question['type'] === 'radio')
                                            <div class="space-y-2">
                                                @foreach ($question['options_array'] as $optionIndex => $option)
                                                    <label class="flex items-center">
                                                        <input type="radio"
                                                            @if ($locale === 'ar') dir="rtl" @endif
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
                                                        @if ($locale === 'ar') dir="rtl" @endif
                                                        wire:model="questionAnswers.{{ $index }}.answer"
                                                        value="true" class="form-checkbox">
                                                    <span
                                                        class="text-sm font-medium text-slate-600 ml-2">{{ __('recruitment.true') }}</span>
                                                </label>
                                            </div>
                                        @elseif ($question['type'] === 'select')
                                            <div class="form-group">
                                                <select wire:model="questionAnswers.{{ $index }}.answer"
                                                    @if ($locale === 'ar') dir="rtl" @endif
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                                    <option value="">{{ __('recruitment.select_option') }}
                                                    </option>
                                                    @foreach ($question['options_array'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif ($question['type'] === 'date')
                                            <div class="form-group">
                                                <input type="date"
                                                    @if ($locale === 'ar') dir="rtl" @endif
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @elseif($question['type'] === 'number')
                                            <div class="form-group">
                                                <input type="number"
                                                    @if ($locale === 'ar') dir="rtl" @endif
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <input type="text"
                                                    @if ($locale === 'ar') dir="rtl" @endif
                                                    wire:model="questionAnswers.{{ $index }}.answer"
                                                    class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                            </div>
                                        @endif
                                        @error('questionAnswers.' . $index . '.answer')
                                            <bdi>
                                                <span
                                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                            </bdi>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <!-- Vacancy Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label">{{ __('recruitment.select_vacancy') }} <span
                                    class="text-danger-500">*</span></label>
                            <select wire:model.live="vacancyId"
                                class="form-control @error('vacancyId') !border-danger-500 @enderror">
                                <option value="">{{ __('recruitment.select_vacancy') }}</option>
                                @foreach ($vacancies as $vacancy)
                                    <option value="{{ $vacancy->id }}">{{ $vacancy->title }}
                                        ({{ $vacancy->position->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vacancyId')
                                <bdi>
                                    <span
                                        class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                </bdi>
                            @enderror
                        </div>
                    @endif

                    <!-- Cover Letter -->
                    <div class="form-group mb-4">
                        <label class="form-label">{{ __('recruitment.cover_letter') }}
                            ({{ __('recruitment.optional') }})</label>
                        <textarea wire:model="coverLetter" class="form-control @error('coverLetter') !border-danger-500 @enderror"
                            @if ($locale === 'ar') dir="rtl" @endif rows="3"
                            placeholder="{{ __('recruitment.cover_letter_placeholder') }}"></textarea>
                        @error('coverLetter')
                            <bdi>
                                <span
                                    class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            </bdi>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    {{-- <div class="form-group mb-4">
                        <label class="flex items-center {{ $locale === 'ar' ? 'flex-row-reverse' : '' }}">
                            <input type="checkbox" wire:model="agreeToTerms" class="form-checkbox"
                                @if ($locale === 'ar') dir="rtl" @endif>
                            <span
                                class="text-sm font-medium text-slate-600 {{ $locale === 'ar' ? 'ml-0 mr-2' : 'ml-2' }}">
                                {{ __('recruitment.agree_terms') }} <a href="#"
                                    class="text-primary-500">{{ __('recruitment.terms_conditions') }}</a>
                                {{ __('recruitment.confirm_accurate_info') }}
                            </span>
                        </label>
                        @error('agreeToTerms')
                            <bdi>
                                <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                            </bdi>
                        @enderror
                    </div> --}}
                @endif

                <!-- Step navigation -->
                <div class="flex justify-between mt-10">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="previousStep"
                            class="btn btn-outline-primary flex items-center btn-sm">
                            <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:arrow-left-bold"></iconify-icon>
                            {{ __('recruitment.previous') }}
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep"
                            class="btn btn-primary flex items-center btn-sm">
                            <span>{{ __('recruitment.next') }}</span>
                            <iconify-icon class="text-sm ltr:ml-2 rtl:mr-2" icon="ph:arrow-right-bold"></iconify-icon>
                        </button>
                    @else
                        <button type="button" wire:click="createApplicant" class="btn btn-success btn-sm"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createApplicant">
                                <iconify-icon class="text-sm ltr:mr-2 rtl:ml-2" icon="ph:check-bold"></iconify-icon>
                                {{ __('recruitment.submit_application') }}
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

        .rtl .step-item:not(:last-child):after {
            right: 50%;
        }

        .ltr .step-item:not(:last-child):after {
            left: 50%;
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

        /* RTL specific styles */
        .rtl .space-x-4> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 1;
            margin-right: calc(1rem * var(--tw-space-x-reverse));
            margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse)));
        }

        .rtl .ml-2 {
            margin-left: 0;
            margin-right: 0.5rem;
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
