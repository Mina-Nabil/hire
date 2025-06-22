<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-5">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900">
                Create New Employee
            </h4>
            <div class="flex items-center">
                <select id="status" class="form-control w-auto @error('status') !border-danger-500 @enderror"
                    wire:model="status">
                    @foreach ($statuses as $statusValue)
                        <option value="{{ $statusValue }}">{{ ucfirst($statusValue) }}</option>
                    @endforeach
                </select>
                @error('status')
                    <span class="text-danger-500 text-xs ml-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
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
                        <!-- Employee Name -->
                        <div class="input-area">
                            <label for="name" class="form-label">Employee Name</label>
                            <input id="name" type="text"
                                class="form-control @error('name') !border-danger-500 @enderror" wire:model.live="name"
                                wire:input="previewUsername">
                            @error('name')
                                <span class="text-danger-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-area">
                            <label for="name_ar" class="form-label">Arabic Name</label>
                            <input id="name_ar" type="text"
                                class="form-control @error('name_ar') !border-danger-500 @enderror"
                                wire:model="name_ar">
                            @error('name_ar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="input-area">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control" wire:model="email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="input-area">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" type="text" class="form-control" wire:model="phone">
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="input-area col-span-2">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" class="form-control" wire:model="address"></textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-area">
                            <label for="mother_name" class="form-label">Mother Name</label>
                            <input id="mother_name" type="text"
                                class="form-control @error('mother_name') !border-danger-500 @enderror"
                                wire:model="mother_name">
                            @error('mother_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="input-area">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input id="nationality" type="text" class="form-control" wire:model="nationality">
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="input-area">
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" class="form-control" wire:model="gender">
                                <option value="">Select Gender</option>
                                @foreach ($genders as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div class="input-area">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input id="birth_date" type="date" class="form-control" wire:model="birth_date">
                            @error('birth_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Birth Place -->
                        <div class="input-area">
                            <label for="birth_place_id" class="form-label">Birth Place</label>
                            <select id="birth_place_id" class="form-control" wire:model="birth_place_id">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('birth_place_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Employment Date -->
                        <div class="input-area">
                            <label for="employment_date" class="form-label">Employment Date</label>
                            <input id="employment_date" type="date" class="form-control"
                                wire:model="employment_date">
                            @error('employment_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- License Required -->
                        <div class="input-area">
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
                                <span class="text-danger">{{ $message }}</span>
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

                <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-md col-span-2">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label for="id_card_file" class="form-label">ID Card Document
                                <iconify-icon wire:loading wire:target="id_card_file"
                                    icon="line-md:loading-twotone-loop" width="18"
                                    height="18"></iconify-icon>
                                @if (isset($applicantDocuments['id_card_url']) && $applicantDocuments['id_card_url'])
                                    <span class="text-xs text-success-500 ml-2">(Loaded from applicant)</span>
                                @endif
                            </label>
                            <div class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                @if ($id_card_file)
                                    <div class="flex items-center justify-center mb-3">
                                        @if (in_array($id_card_file->getClientOriginalExtension(), ['pdf']))
                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48" height="48"
                                                class="text-red-500"></iconify-icon>
                                        @else
                                            @if (method_exists($id_card_file, 'temporaryUrl'))
                                                <img src="{{ $id_card_file->temporaryUrl() }}"
                                                    class="h-40 max-w-full rounded-md object-contain"
                                                    alt="ID Card Preview">
                                            @else
                                                <div class="h-40 w-full bg-slate-100 rounded-md flex items-center justify-center">
                                                    <iconify-icon icon="mingcute:file-image-fill" width="48" height="48"
                                                        class="text-slate-400"></iconify-icon>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500">
                                        {{ $id_card_file->getClientOriginalName() }}</p>
                                    <button type="button" class="text-sm text-red-500 mt-2"
                                        wire:click="$set('id_card_file', null)">
                                        Remove File
                                    </button>
                                @else
                                    <label for="id_card_file_input" class="cursor-pointer block">
                                        <iconify-icon icon="mingcute:upload-line" width="32" height="32"
                                            class="text-slate-400 mx-auto"></iconify-icon>
                                        <p class="mt-2 text-sm text-slate-500">Click to upload or
                                            drag
                                            and drop</p>
                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                            10MB)
                                        </p>
                                        <input id="id_card_file_input" type="file" class="hidden"
                                            wire:model="id_card_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                    </label>
                                @endif
                            </div>
                            @error('id_card_file')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-12">
                            <label for="id_number" class="form-label">ID Number</label>
                            <input type="text"
                                class="form-control @error('id_number') !border-danger-500 @enderror"
                                wire:model="id_number">
                            @error('id_number')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-12 xl:col-span-6">
                            <label for="id_issue_date" class="form-label">Issue Date</label>
                            <input type="date"
                                class="form-control @error('id_issue_date') !border-danger-500 @enderror"
                                wire:model="id_issue_date">
                            @error('id_issue_date')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-12 xl:col-span-6">
                            <label for="id_expiry_date" class="form-label">Expiry Date</label>
                            <input type="date"
                                class="form-control @error('id_expiry_date') !border-danger-500 @enderror"
                                wire:model="id_expiry_date">
                            @error('id_expiry_date')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Documents Section -->
                <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-md col-span-2">
                    <h5 class="font-medium text-xl text-slate-900 dark:text-white mb-5">
                        Additional Documents
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Birth Certificate -->
                        <div>
                            <label for="birth_certificate_file" class="form-label">Birth Certificate
                                <iconify-icon wire:loading wire:target="birth_certificate_file"
                                    icon="line-md:loading-twotone-loop" width="18" height="18"></iconify-icon>
                                @if (isset($applicantDocuments['birth_certificate_url']) && $applicantDocuments['birth_certificate_url'])
                                    <span class="text-xs text-success-500 ml-2">(Loaded from applicant)</span>
                                @endif
                            </label>
                            <div class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                @if ($birth_certificate_file)
                                    <div class="flex items-center justify-center mb-3">
                                        @if (in_array($birth_certificate_file->getClientOriginalExtension(), ['pdf']))
                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48" height="48"
                                                class="text-red-500"></iconify-icon>
                                        @else
                                            @if (method_exists($birth_certificate_file, 'temporaryUrl'))
                                                <img src="{{ $birth_certificate_file->temporaryUrl() }}"
                                                    class="h-32 max-w-full rounded-md object-contain"
                                                    alt="Birth Certificate Preview">
                                            @else
                                                <div class="h-32 w-full bg-slate-100 rounded-md flex items-center justify-center">
                                                    <iconify-icon icon="mingcute:file-image-fill" width="48" height="48"
                                                        class="text-slate-400"></iconify-icon>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500">
                                        {{ $birth_certificate_file->getClientOriginalName() }}</p>
                                    <button type="button" class="text-sm text-red-500 mt-2"
                                        wire:click="$set('birth_certificate_file', null)">
                                        Remove File
                                    </button>
                                @else
                                    <label for="birth_certificate_file_input" class="cursor-pointer block">
                                        <iconify-icon icon="mingcute:upload-line" width="32" height="32"
                                            class="text-slate-400 mx-auto"></iconify-icon>
                                        <p class="mt-2 text-sm text-slate-500">Click to upload or drag and drop</p>
                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max 10MB)</p>
                                        <input id="birth_certificate_file_input" type="file" class="hidden"
                                            wire:model="birth_certificate_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                    </label>
                                @endif
                            </div>
                            @error('birth_certificate_file')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- College Certificate -->
                        <div>
                            <label for="college_certificate_file" class="form-label">College Certificate
                                <iconify-icon wire:loading wire:target="college_certificate_file"
                                    icon="line-md:loading-twotone-loop" width="18" height="18"></iconify-icon>
                                @if (isset($applicantDocuments['college_certificate_url']) && $applicantDocuments['college_certificate_url'])
                                    <span class="text-xs text-success-500 ml-2">(Loaded from applicant)</span>
                                @endif
                            </label>
                            <div class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                @if ($college_certificate_file)
                                    <div class="flex items-center justify-center mb-3">
                                        @if (in_array($college_certificate_file->getClientOriginalExtension(), ['pdf']))
                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48" height="48"
                                                class="text-red-500"></iconify-icon>
                                        @else
                                            @if (method_exists($college_certificate_file, 'temporaryUrl'))
                                                <img src="{{ $college_certificate_file->temporaryUrl() }}"
                                                    class="h-32 max-w-full rounded-md object-contain"
                                                    alt="College Certificate Preview">
                                            @else
                                                <div class="h-32 w-full bg-slate-100 rounded-md flex items-center justify-center">
                                                    <iconify-icon icon="mingcute:file-image-fill" width="48" height="48"
                                                        class="text-slate-400"></iconify-icon>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500">
                                        {{ $college_certificate_file->getClientOriginalName() }}</p>
                                    <button type="button" class="text-sm text-red-500 mt-2"
                                        wire:click="$set('college_certificate_file', null)">
                                        Remove File
                                    </button>
                                @else
                                    <label for="college_certificate_file_input" class="cursor-pointer block">
                                        <iconify-icon icon="mingcute:upload-line" width="32" height="32"
                                            class="text-slate-400 mx-auto"></iconify-icon>
                                        <p class="mt-2 text-sm text-slate-500">Click to upload or drag and drop</p>
                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max 10MB)</p>
                                        <input id="college_certificate_file_input" type="file" class="hidden"
                                            wire:model="college_certificate_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                    </label>
                                @endif
                            </div>
                            @error('college_certificate_file')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Army Certificate -->
                        <div>
                            <label for="army_certificate_file" class="form-label">Army Certificate
                                <iconify-icon wire:loading wire:target="army_certificate_file"
                                    icon="line-md:loading-twotone-loop" width="18" height="18"></iconify-icon>
                                @if (isset($applicantDocuments['army_certificate_url']) && $applicantDocuments['army_certificate_url'])
                                    <span class="text-xs text-success-500 ml-2">(Loaded from applicant)</span>
                                @endif
                            </label>
                            <div class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                @if ($army_certificate_file)
                                    <div class="flex items-center justify-center mb-3">
                                        @if (in_array($army_certificate_file->getClientOriginalExtension(), ['pdf']))
                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48" height="48"
                                                class="text-red-500"></iconify-icon>
                                        @else
                                            @if (method_exists($army_certificate_file, 'temporaryUrl'))
                                                <img src="{{ $army_certificate_file->temporaryUrl() }}"
                                                    class="h-32 max-w-full rounded-md object-contain"
                                                    alt="Army Certificate Preview">
                                            @else
                                                <div class="h-32 w-full bg-slate-100 rounded-md flex items-center justify-center">
                                                    <iconify-icon icon="mingcute:file-image-fill" width="48" height="48"
                                                        class="text-slate-400"></iconify-icon>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500">
                                        {{ $army_certificate_file->getClientOriginalName() }}</p>
                                    <button type="button" class="text-sm text-red-500 mt-2"
                                        wire:click="$set('army_certificate_file', null)">
                                        Remove File
                                    </button>
                                @else
                                    <label for="army_certificate_file_input" class="cursor-pointer block">
                                        <iconify-icon icon="mingcute:upload-line" width="32" height="32"
                                            class="text-slate-400 mx-auto"></iconify-icon>
                                        <p class="mt-2 text-sm text-slate-500">Click to upload or drag and drop</p>
                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max 10MB)</p>
                                        <input id="army_certificate_file_input" type="file" class="hidden"
                                            wire:model="army_certificate_file" accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                    </label>
                                @endif
                            </div>
                            @error('army_certificate_file')
                                <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- User Account Information Section -->
                <div class="bg-slate-50 dark:bg-slate-900 p-5 rounded-md col-span-2 ">
                    <h5 class="font-medium text-xl text-slate-900 dark:text-white mb-5">
                        User Account Information
                    </h5>

                    @if (!empty($previewedUsername))
                        <div
                            class="mt-4 bg-white dark:bg-slate-800 p-4 rounded-md border border-slate-200 dark:border-slate-700">
                            <h6 class="text-sm font-medium text-slate-900 dark:text-white mb-2">Username Preview</h6>
                            <div class="flex items-center">
                                <span
                                    class="text-sm text-info-600 dark:text-info-400 font-medium">{{ $previewedUsername }}</span>
                                <span class="ml-2 text-xs text-slate-500">(Generated from the employee name)</span>
                            </div>
                            <div class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                The password is: {{ $password }}
                            </div>
                            @if ($usernameHasSuffix)
                                <div class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                    <iconify-icon icon="material-symbols:info-outline"
                                        class="inline-block mr-1"></iconify-icon>
                                    The base username "{{ $baseUsername }}" is already taken, so a number has been
                                    added.
                                </div>
                                <div class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                            @endif
                        </div>
                    @endif
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
