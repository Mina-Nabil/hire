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

                @endif


                <!-- Step 2: Vacancy & Application -->
                @if ($currentStep === 2)
                    <h4 class="text-xl font-medium mb-5">{{ __('recruitment.vacancy_application_details') }}</h4>

                    @if ($selectedVacancy)
                        <!-- Vacancy Information -->
                        <div class="flex justify-between">
                            <div class="w-full p-4 bg-slate-50 rounded-md mb-6">
                                <h5 class="font-medium mb-2">{{ __('recruitment.vacancy_details') }}:</h5>
                                <p><strong>{{ __('recruitment.position') }}:</strong>
                                    {{ $selectedVacancy->position?->name }}</p>
                                <p><strong>{{ __('recruitment.department') }}:</strong>
                                    {{ $selectedVacancy->position?->department?->name }}</p>
                                <p><strong>{{ __('recruitment.opening_date') }}:</strong>
                                    {{ $selectedVacancy->created_at->format('d M Y') }}
                                </p>
                                <p><strong>{{ __('recruitment.closing_date') }}:</strong>
                                    {{ $selectedVacancy->closing_date }}
                                </p>
                                <p><strong>{{ __('recruitment.status') }}:</strong> <span
                                        class="badge {{ $selectedVacancy->status === 'open' ? 'bg-success-500' : 'bg-danger-500' }}">{{ ucfirst($selectedVacancy->status) }}</span>
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
                        @if ($selectedVacancy->vacancy_slots && $selectedVacancy->vacancy_slots->count() > 0)
                            <div class="mb-6">
                                <h5 class="font-medium mb-4">{{ __('recruitment.preferred_interview_slot') }}</h5>

                                <x-select wire:model="slotId" class="@error('slotId') !border-danger-500 @enderror">
                                    <option value="">{{ __('recruitment.all_slots_ok') }}</option>
                                    @foreach ($selectedVacancy->vacancy_slots as $slot)
                                        <option value="{{ $slot->id }}">
                                            {{ $slot->date->format('d M Y') }} -
                                            {{ $slot->start_time->format('H:i') }} to
                                            {{ $slot->end_time->format('H:i') }}
                                        </option>
                                    @endforeach
                                    @error('slotId')
                                    <bdi>
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    </bdi>
                                @enderror
                                </x-select>

                            </div>
                        @endif
                    @else
                        <!-- Vacancy Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label">{{ __('recruitment.select_vacancy') }} <span
                                    class="text-danger-500">*</span></label>
                            <select wire:model.live="vacancyId"
                                class="form-control @error('vacancyId') !border-danger-500 @enderror"
                                @if ($locale === 'ar') dir="rtl" @endif>
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

                    {{-- <!-- Base Questions (if any) -->
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
                                            <textarea wire:model="questionAnswers.{{ $index }}.answer" @if ($locale === 'ar') dir="rtl" @endif
                                                class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror" rows="3"></textarea>
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
                                            <input type="date" @if ($locale === 'ar') dir="rtl" @endif
                                                wire:model="questionAnswers.{{ $index }}.answer"
                                                class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                        </div>
                                    @elseif($question['type'] === 'number')
                                        <div class="form-group">
                                            <input type="number" @if ($locale === 'ar') dir="rtl" @endif
                                                wire:model="questionAnswers.{{ $index }}.answer"
                                                class="form-control @error('questionAnswers.' . $index . '.answer') !border-danger-500 @enderror">
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <input type="text" @if ($locale === 'ar') dir="rtl" @endif
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
 --}}



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
