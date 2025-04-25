<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <div>
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    {{ $employee->name }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">Employee</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-5">

        <div class="xl:col-span-3 lg:col-span-4 col-span-12">
            <div class="card">
                <div class="card-body">

                    <!-- BEGIN: Files Card -->


                    <ul class="divide-y divide-slate-100 dark:divide-slate-700 cursor-pointer">

                        <li wire:click="changeSection('info')"
                            class="block py-[8px] p-6  {{ $section == 'info' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Information

                                @if ($section == 'info')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'info' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('id_card')"
                            class="block py-[8px] p-6  {{ $section == 'id_card' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                ID Card

                                @if ($section == 'id_card')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'id_card' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('birth_certificate')"
                            class="block py-[8px] p-6  {{ $section == 'birth_certificate' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Birth Certificate

                                @if ($section == 'birth_certificate')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'birth_certificate' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('army_service_paper')"
                            class="block py-[8px] p-6  {{ $section == 'army_service_paper' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Army Service Paper

                                @if ($section == 'army_service_paper')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'army_service_paper' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('driver_license')"
                            class="block py-[8px] p-6  {{ $section == 'driver_license' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Driver License

                                @if ($section == 'driver_license')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'driver_license' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('employee_contract')"
                            class="block py-[8px] p-6  {{ $section == 'employee_contract' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Employee Contract

                                @if ($section == 'employee_contract')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'employee_contract' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('employee_s1_doc')"
                            class="block py-[8px] p-6  {{ $section == 'employee_s1_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Employee S1 Doc

                                @if ($section == 'employee_s1_doc')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'employee_s1_doc' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('employee_s2_doc')"
                            class="block py-[8px] p-6  {{ $section == 'employee_s2_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Employee S2 Doc

                                @if ($section == 'employee_s2_doc')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'employee_s2_doc' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('employee_s6_doc')"
                            class="block py-[8px] p-6  {{ $section == 'employee_s6_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Employee S6 Doc

                                @if ($section == 'employee_s6_doc')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'employee_s6_doc' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('police_record')"
                            class="block py-[8px] p-6  {{ $section == 'police_record' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                Police Record

                                @if ($section == 'police_record')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'police_record' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li wire:click="changeSection('hr_letter')"
                            class="block py-[8px] p-6  {{ $section == 'hr_letter' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                HR Letter

                                @if ($section == 'hr_letter')
                                    <div class="flex-none">
                                        <button type="button"
                                            class="text-xs text-slate-900 dark:text-white {{ $section == 'hr_letter' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                            <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                height="25"></iconify-icon>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>



                    </ul>
                    <!-- END: FIles Card -->
                </div>
            </div>
        </div>

        <div class="xl:col-span-8 lg:col-span-7 col-span-12">
            @if ($section == 'info')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Base Information
                        </h4>

                        @if (!$employee->idCard)
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditBaseInfoModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-12 gap-5">
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Name</label>
                                    <div class="text-base text-slate-900 dark:text-white">{{ $employee->name }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Email</label>
                                    <div class="text-base text-slate-900 dark:text-white">{{ $employee->email }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Phone</label>
                                    <div class="text-base text-slate-900 dark:text-white">{{ $employee->phone }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Address</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->address }}</div>
                                </div>
                            </div>
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Nationality</label>
                                    <div class="text-base text-slate-900 dark:text-white">{{ $employee->nationality }}
                                    </div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Gender</label>
                                    <div class="text-base text-slate-900 dark:text-white">{{ $employee->gender }}
                                    </div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Birth Date</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->birth_date?->format('d/m/Y') }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Employment
                                        Date</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->employment_date?->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Employee Information
                        </h4>

                        <button type="button" class="text-slate-900 dark:text-white"
                            wire:click="openEditEmployeeInfoModal">
                            <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                        </button>
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-12 gap-5">
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Insurance
                                        Number</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->insurance_number ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Insurance
                                        Amount</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->insurance_amount ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Academic
                                        Qualification</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->academic_qualification ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">University</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->university ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Graduation
                                        Year</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->graduation_year ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Military
                                        Status</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->military_status ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Marital
                                        Status</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->info?->marital_status ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($section == 'id_card')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            ID Card Information
                        </h4>

                        <button type="button" class="text-slate-900 dark:text-white"
                            wire:click="openEditIdCardModal">
                            <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                        </button>
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->idCard)
                            <div class="grid grid-cols-12 gap-5 pb-5">
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">ID Number</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->idCard->id_number }}
                                        </div>
                                    </div>
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->idCard->issue_date }}
                                        </div>
                                    </div>
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->idCard->expiry_date }}
                                        </div>
                                    </div>
                                </div>
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                        <div class="mt-3">
                                            @php
                                                $fileExt = $this->getFileExtension($employee->idCard->file_path);
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <!-- PDF Preview -->
                                                <div class="border border-slate-200 rounded-md p-2">
                                                    <iframe src="{{ $employee->idCard->file_path }}" width="100%"
                                                        height="400" class="border-0"></iframe>
                                                </div>
                                            @else
                                                <!-- Image Preview -->
                                                <div class="mb-3">
                                                    <img src="{{ $employee->idCard->file_path }}" alt="ID Card"
                                                        class="max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif

                                            <!-- Download Button -->
                                            <button wire:click="downloadIdCard" type="button"
                                                class="btn  btn-dark btn-sm mt-2" style="min-width: 150px;">
                                                <span class="inline-flex justify-center" wire:loading.remove
                                                    wire:target="downloadIdCard">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="18" height="18"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadIdCard"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:id-card-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No ID Card Document Found</h5>
                                <p class="text-slate-500 mb-5">Please upload an ID card document for this employee</p>
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditIdCardModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload ID Card
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($section == 'birth_certificate')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Birth Certificate Information
                        </h4>

                        <button type="button" class="text-slate-900 dark:text-white"
                            wire:click="openEditBirthCertificateModal">
                            <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                        </button>
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->birthCertificate)
                            <div class="grid grid-cols-12 gap-5 pb-5">
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Certificate
                                            Type</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->birthCertificate->type }}
                                        </div>
                                    </div>
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->birthCertificate->issue_date }}
                                        </div>
                                    </div>
                                    @if ($employee->birthCertificate->expiry_date)
                                        <div class="mb-5 text-wrap">
                                            <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry
                                                Date</label>
                                            <div class="text-base text-slate-900 dark:text-white">
                                                {{ $employee->birthCertificate->expiry_date }}
                                                {{-- @if ($employee->birthCertificate->expiry_date?->isPast())
                                                <span class="text-danger-500 text-xs font-medium">(Expired)</span>
                                            @elseif ($employee->birthCertificate->expiry_date?->diffInDays(now()) < 30)
                                                <span class="text-warning-500 text-xs font-medium">(Expiring soon)</span>
                                            @endif --}}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                        <div class="mt-3">
                                            @php
                                                $fileExt = $this->getFileExtension(
                                                    $employee->birthCertificate->file_path,
                                                );
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <!-- PDF Preview -->
                                                <div class="border border-slate-200 rounded-md p-2">
                                                    <iframe src="{{ $employee->birthCertificate->file_path }}"
                                                        width="100%" height="400" class="border-0"></iframe>
                                                </div>
                                            @else
                                                <!-- Image Preview -->
                                                <div class="mb-3">
                                                    <img src="{{ $employee->birthCertificate->file_path }}"
                                                        alt="Birth Certificate"
                                                        class="max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif

                                            <!-- Download Button -->
                                            <button wire:click="downloadBirthCertificate" type="button"
                                                class="btn btn-dark btn-sm mt-2" style="min-width: 150px;">
                                                <span class="inline-flex justify-center" wire:loading.remove
                                                    wire:target="downloadBirthCertificate">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="18" height="18"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadBirthCertificate"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Birth Certificate Found</h5>
                                <p class="text-slate-500 mb-5">Please upload a birth certificate for this employee</p>
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditBirthCertificateModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Birth Certificate
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'army_service_paper')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Army Service Paper Information
                        </h4>

                        <button type="button" class="text-slate-900 dark:text-white"
                            wire:click="openEditArmyServicePaperModal">
                            <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                        </button>
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->armyServicePaper)
                            <div class="grid grid-cols-12 gap-5 pb-5">
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Certificate
                                            Type</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->armyServicePaper->type }}
                                        </div>
                                    </div>
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->armyServicePaper->issue_date }}
                                        </div>
                                    </div>
                                    @if ($employee->armyServicePaper->expiry_date)
                                        <div class="mb-5 text-wrap">
                                            <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry
                                                Date</label>
                                            <div class="text-base text-slate-900 dark:text-white">
                                                {{ $employee->armyServicePaper->expiry_date }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                        <div class="mt-3">
                                            @php
                                                $fileExt = $this->getFileExtension(
                                                    $employee->armyServicePaper->file_path,
                                                );
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <!-- PDF Preview -->
                                                <div class="border border-slate-200 rounded-md p-2">
                                                    <iframe src="{{ $employee->armyServicePaper->file_path }}"
                                                        width="100%" height="400" class="border-0"></iframe>
                                                </div>
                                            @else
                                                <!-- Image Preview -->
                                                <div class="mb-3">
                                                    <img src="{{ $employee->armyServicePaper->file_path }}"
                                                        alt="Army Service Paper"
                                                        class="max-w-full h-auto rounded-md shadow-sm">
                                                </div>
                                            @endif

                                            <!-- Download Button -->
                                            <button wire:click="downloadArmyServicePaper" type="button"
                                                class="btn btn-dark btn-sm mt-2" style="min-width: 150px;">
                                                <span class="inline-flex justify-center" wire:loading.remove
                                                    wire:target="downloadArmyServicePaper">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="18" height="18"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadArmyServicePaper"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Army Service Paper Found</h5>
                                <p class="text-slate-500 mb-5">Please upload an army service paper for this employee
                                </p>
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditArmyServicePaperModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Army Service Paper
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'driver_license')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Driver License Information
                        </h4>

                        <button type="button" class="text-slate-900 dark:text-white"
                            wire:click="openEditDriverLicenseModal">
                            <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                        </button>
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->driverLicense)
                        <div class="grid grid-cols-12 gap-5 pb-5">
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                        Date</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->driverLicense->issue_date }}
                                    </div>
                                </div>
                                @if ($employee->driverLicense->expiry_date)
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->driverLicense->expiry_date }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                    <div class="mt-3">
                                        @php
                                            $fileExt = $this->getFileExtension(
                                                $employee->driverLicense->file_path,
                                            );
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <!-- PDF Preview -->
                                            <div class="border border-slate-200 rounded-md p-2">
                                                <iframe src="{{ $employee->driverLicense->file_path }}"
                                                    width="100%" height="400" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <!-- Image Preview -->
                                            <div class="mb-3">
                                                <img src="{{ $employee->driverLicense->file_path }}"
                                                    alt="Driver License"
                                                    class="max-w-full h-auto rounded-md shadow-sm">
                                            </div>
                                        @endif

                                        <!-- Download Button -->
                                        <button wire:click="downloadDriverLicense" type="button"
                                            class="btn btn-dark btn-sm mt-2" style="min-width: 150px;">
                                            <span class="inline-flex justify-center" wire:loading.remove
                                                wire:target="downloadDriverLicense">
                                                <iconify-icon icon="fluent:arrow-download-28-filled"
                                                    class="mr-1" width="18" height="18"></iconify-icon>
                                                Download Document
                                            </span>
                                            <iconify-icon wire:loading wire:target="downloadDriverLicense"
                                                icon="line-md:loading-twotone-loop" width="18"
                                                height="18"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-5">
                                <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                    class="text-slate-400"></iconify-icon>
                            </div>
                            <h5 class="text-xl font-semibold mb-4">No Driver License Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a driver license for this employee</p>
                            <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                wire:click="openEditDriverLicenseModal">
                                <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                    class="mr-1"></iconify-icon>
                                Upload Driver License
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @elseif ($section === 'employee_s1_doc')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        Employee S1 Doc Information
                    </h4>

                    <button type="button" class="text-slate-900 dark:text-white"
                        wire:click="openEditEmployeeS1DocModal">
                        <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                    </button>
                </div>
                <div class="card-body p-6">
                    @if ($employee->employeeS1Doc)
                    <div class="grid grid-cols-12 gap-5 pb-5">
                        <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                            <div class="mb-5 text-wrap">
                                <label class="text-xs text-slate-500 dark:text-slate-400 m-0">S1 Number</label>
                                <div class="text-base text-slate-900 dark:text-white">
                                    {{ $employee->employeeS1Doc->s1_number }}
                                </div>
                            </div>
                            <div class="mb-5 text-wrap">
                                <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue Date</label>
                                <div class="text-base text-slate-900 dark:text-white">
                                    {{ $employee->employeeS1Doc->issue_date }}
                                </div>
                            </div>
                            @if ($employee->employeeS1Doc->expiry_date)
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry Date</label>
                                    <div class="text-base text-slate-900 dark:text-white">
                                        {{ $employee->employeeS1Doc->expiry_date }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                            <div class="mb-5 text-wrap">
                                <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                <div class="mt-3">
                                    @php
                                        $fileExt = $this->getFileExtension(
                                            $employee->employeeS1Doc->file_path,
                                        );
                                    @endphp

                                    @if ($fileExt == 'pdf')
                                        <!-- PDF Preview -->
                                        <div class="border border-slate-200 rounded-md p-2">
                                            <iframe src="{{ $employee->employeeS1Doc->file_path }}"
                                                width="100%" height="400" class="border-0"></iframe>
                                        </div>
                                    @else
                                        <!-- Image Preview -->
                                        <div class="mb-3">
                                            <img src="{{ $employee->employeeS1Doc->file_path }}"
                                                alt="Employee S1 Doc"
                                                class="max-w-full h-auto rounded-md shadow-sm">
                                        </div>
                                    @endif

                                    <!-- Download Button -->
                                    <button wire:click="downloadEmployeeS1Doc" type="button"
                                        class="btn btn-dark btn-sm mt-2" style="min-width: 150px;">
                                        <span class="inline-flex justify-center" wire:loading.remove
                                            wire:target="downloadEmployeeS1Doc">
                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                class="mr-1" width="18" height="18"></iconify-icon>
                                            Download Document
                                        </span>
                                        <iconify-icon wire:loading wire:target="downloadEmployeeS1Doc"
                                            icon="line-md:loading-twotone-loop" width="18"
                                            height="18"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-5">
                            <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                class="text-slate-400"></iconify-icon>
                        </div>
                        <h5 class="text-xl font-semibold mb-4">No Employee S1 Doc Found</h5>
                        <p class="text-slate-500 mb-5">Please upload an S1 document for this employee</p>
                        <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                            wire:click="openEditEmployeeS1DocModal">
                            <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                class="mr-1"></iconify-icon>
                            Upload S1 Document
                        </button>
                    </div>
                @endif
                </div>
            </div>
            @elseif ($section === 'employee_s2_doc')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        Employee S2 Doc Information
                    </h4>

                    <button type="button" class="text-slate-900 dark:text-white"
                        wire:click="openEditEmployeeS2DocModal">
                        <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                    </button>
                </div>
                <div class="card-body p-6">
                    @if ($employee->employeeS2Doc && count($employee->employeeS2Doc) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Year</th>
                                        <th scope="col" class="table-th">S2 Amount</th>
                                        <th scope="col" class="table-th">Issue Date</th>
                                        <th scope="col" class="table-th">Expiry Date</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach($employee->employeeS2Doc as $index => $s2Doc)
                                        <tr>
                                            <td class="table-td">{{ $s2Doc->year }}</td>
                                            <td class="table-td">{{ $s2Doc->s2_amount }}</td>
                                            <td class="table-td">{{ $s2Doc->issue_date }}</td>
                                            <td class="table-td">{{ $s2Doc->expiry_date ?? 'N/A' }}</td>
                                            <td class="table-td">
                                                <button wire:click="downloadEmployeeS2Doc({{ $s2Doc->id }})" type="button" 
                                                    class="btn btn-dark btn-sm inline-flex justify-center" style="min-width: 100px;">
                                                    <span class="inline-flex justify-center" wire:loading.remove
                                                        wire:target="downloadEmployeeS2Doc({{ $s2Doc->id }})">
                                                        <iconify-icon icon="fluent:arrow-download-28-filled"
                                                            class="mr-1" width="18" height="18"></iconify-icon>
                                                        Download
                                                    </span>
                                                    <iconify-icon wire:loading wire:target="downloadEmployeeS2Doc({{ $s2Doc->id }})"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-5">
                                <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                    class="text-slate-400"></iconify-icon>
                            </div>
                            <h5 class="text-xl font-semibold mb-4">No Employee S2 Docs Found</h5>
                            <p class="text-slate-500 mb-5">Please upload an S2 document for this employee</p>
                            <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                wire:click="openEditEmployeeS2DocModal">
                                <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                    class="mr-1"></iconify-icon>
                                Upload S2 Document
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @elseif ($section === 'employee_s6_doc')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        Employee S6 Doc Information
                    </h4>

                    <button type="button" class="text-slate-900 dark:text-white"
                        wire:click="openEditEmployeeS6DocModal">
                        <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                    </button>
                </div>
                <div class="card-body p-6">
                    @if ($employee->employeeS6Doc && count($employee->employeeS6Doc) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">S6 Number</th>
                                        <th scope="col" class="table-th">Leaving Reason</th>
                                        <th scope="col" class="table-th">Issue Date</th>
                                        <th scope="col" class="table-th">Expiry Date</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @foreach($employee->employeeS6Doc as $index => $s6Doc)
                                        <tr>
                                            <td class="table-td">{{ $s6Doc->s6_number }}</td>
                                            <td class="table-td">{{ $s6Doc->leaving_reason }}</td>
                                            <td class="table-td">{{ $s6Doc->issue_date }}</td>
                                            <td class="table-td">{{ $s6Doc->expiry_date ?? 'N/A' }}</td>
                                            <td class="table-td">
                                                <button wire:click="downloadEmployeeS6Doc({{ $s6Doc->id }})" type="button" 
                                                    class="btn btn-dark btn-sm inline-flex justify-center" style="min-width: 100px;">
                                                    <span class="inline-flex justify-center" wire:loading.remove
                                                        wire:target="downloadEmployeeS6Doc({{ $s6Doc->id }})">
                                                        <iconify-icon icon="fluent:arrow-download-28-filled"
                                                            class="mr-1" width="18" height="18"></iconify-icon>
                                                        Download
                                                    </span>
                                                    <iconify-icon wire:loading wire:target="downloadEmployeeS6Doc({{ $s6Doc->id }})"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-5">
                                <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                    class="text-slate-400"></iconify-icon>
                            </div>
                            <h5 class="text-xl font-semibold mb-4">No Employee S6 Docs Found</h5>
                            <p class="text-slate-500 mb-5">Please upload an S6 document for this employee</p>
                            <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                wire:click="openEditEmployeeS6DocModal">
                                <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                    class="mr-1"></iconify-icon>
                                Upload S6 Document
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @elseif ($section === 'police_record')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        Police Record Information
                    </h4>

                    <button type="button" class="text-slate-900 dark:text-white"
                        wire:click="openEditPoliceRecordModal">
                        <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                    </button>
                </div>
                <div class="card-body p-6">
                    @if ($employee->policeRecords && count($employee->policeRecords) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($employee->policeRecords as $index => $policeRecord)
                                <div class="card border border-slate-200 dark:border-slate-700">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">Police Record</h5>
                                        <div class="flex space-x-2">
                                            <button type="button" wire:click="openEditSpecificPoliceRecordModal({{ $policeRecord->id }})" class="text-primary-500">
                                                <iconify-icon icon="mingcute:edit-line" width="18" height="18"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <!-- Document Preview -->
                                        <div class="mb-3 flex justify-center">
                                            @php
                                                $fileExt = $this->getFileExtension($policeRecord->file_path);
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <div class="flex items-center justify-center mb-2">
                                                    <iconify-icon icon="mingcute:file-pdf-fill" width="64"
                                                        height="64" class="text-red-500"></iconify-icon>
                                                </div>
                                            @else
                                                <img src="{{ $policeRecord->file_path }}" 
                                                     alt="Police Record" 
                                                     class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                            @endif
                                        </div>

                                        <!-- Document Info -->
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Issue Date:</span>
                                                <span class="text-sm font-medium">{{ $policeRecord->issue_date }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Expiry Date:</span>
                                                <span class="text-sm font-medium">{{ $policeRecord->expiry_date ? $policeRecord->expiry_date : 'N/A' }}</span>
                                            </div>

                                            <!-- Download Button -->
                                            <div class="mt-3 text-center">
                                                <button wire:click="downloadPoliceRecord({{ $policeRecord->id }})" type="button" 
                                                    class="btn btn-dark btn-sm w-full">
                                                    <span class="inline-flex items-center justify-center" wire:loading.remove
                                                        wire:target="downloadPoliceRecord({{ $policeRecord->id }})">
                                                        <iconify-icon icon="fluent:arrow-download-28-filled"
                                                            class="mr-1" width="16" height="16"></iconify-icon>
                                                        Download
                                                    </span>
                                                    <iconify-icon wire:loading wire:target="downloadPoliceRecord({{ $policeRecord->id }})"
                                                        icon="line-md:loading-twotone-loop" width="16"
                                                        height="16"></iconify-icon>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-5">
                                <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                    class="text-slate-400"></iconify-icon>
                            </div>
                            <h5 class="text-xl font-semibold mb-4">No Police Records Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a police record for this employee</p>
                            <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                wire:click="openEditPoliceRecordModal">
                                <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                    class="mr-1"></iconify-icon>
                                Upload Police Record
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @elseif ($section === 'hr_letter')
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                        HR Letter Information
                    </h4>

                    <button type="button" class="text-slate-900 dark:text-white"
                        wire:click="openEditHrLetterModal">
                        <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                    </button>
                </div>
                <div class="card-body p-6">
                    @if ($employee->hrLetters && count($employee->hrLetters) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($employee->hrLetters as $index => $hrLetter)
                                <div class="card border border-slate-200 dark:border-slate-700">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">HR Letter</h5>
                                        <div class="flex space-x-2">
                                            <button type="button" wire:click="openEditSpecificHrLetterModal({{ $hrLetter->id }})" class="text-primary-500">
                                                <iconify-icon icon="mingcute:edit-line" width="18" height="18"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <!-- Document Preview -->
                                        <div class="mb-3 flex justify-center">
                                            @php
                                                $fileExt = $this->getFileExtension($hrLetter->file_path);
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <div class="flex items-center justify-center mb-2">
                                                    <iconify-icon icon="mingcute:file-pdf-fill" width="64"
                                                        height="64" class="text-red-500"></iconify-icon>
                                                </div>
                                            @else
                                                <img src="{{ $hrLetter->file_path }}" 
                                                     alt="HR Letter" 
                                                     class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                            @endif
                                        </div>

                                        <!-- Document Info -->
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Issue Date:</span>
                                                <span class="text-sm font-medium">{{ $hrLetter->issue_date->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Expiry Date:</span>
                                                <span class="text-sm font-medium">{{ $hrLetter->expiry_date ? $hrLetter->expiry_date->format('d/m/Y') : 'N/A' }}</span>
                                            </div>

                                            <!-- Download Button -->
                                            <div class="mt-3 text-center">
                                                <button wire:click="downloadHrLetter({{ $hrLetter->id }})" type="button" 
                                                    class="btn btn-dark btn-sm w-full">
                                                    <span class="inline-flex items-center justify-center" wire:loading.remove
                                                        wire:target="downloadHrLetter({{ $hrLetter->id }})">
                                                        <iconify-icon icon="fluent:arrow-download-28-filled"
                                                            class="mr-1" width="16" height="16"></iconify-icon>
                                                        Download
                                                    </span>
                                                    <iconify-icon wire:loading wire:target="downloadHrLetter({{ $hrLetter->id }})"
                                                        icon="line-md:loading-twotone-loop" width="16"
                                                        height="16"></iconify-icon>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-5">
                                <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                    class="text-slate-400"></iconify-icon>
                            </div>
                            <h5 class="text-xl font-semibold mb-4">No HR Letters Found</h5>
                            <p class="text-slate-500 mb-5">Please upload an HR letter for this employee</p>
                            <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                wire:click="openEditHrLetterModal">
                                <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                    class="mr-1"></iconify-icon>
                                Upload HR Letter
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            
        </div>
    </div>

    <!-- Driver License Section -->


    <!-- Driver License Edit Modal -->
    @if ($editDriverLicenseModal)
        <div>
            <div id="editDriverLicenseModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editDriverLicenseModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Driver License
                                </h3>
                                <button wire:click="closeEditDriverLicenseModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>

                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                @if (!$keep_existing_driver_license)
                                    <div class="col-span-12">
                                        <label for="driver_license_file" class="form-label">Driver License
                                            Document
                                            <iconify-icon wire:loading wire:target="driver_license_file"
                                                icon="line-md:loading-twotone-loop" width="18"
                                                height="18"></iconify-icon></label>
                                        <div
                                            class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                            @if ($driver_license_file)
                                                <div class="flex items-center justify-center mb-3">
                                                    @if (in_array($driver_license_file->getClientOriginalExtension(), ['pdf']))
                                                        <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                            height="48" class="text-red-500"></iconify-icon>
                                                    @else
                                                        <img src="{{ $driver_license_file->temporaryUrl() }}"
                                                            class="h-40 max-w-full rounded-md object-contain"
                                                            alt="Driver License Preview">
                                                    @endif
                                                </div>
                                                <p class="text-sm text-slate-500">
                                                    {{ $driver_license_file->getClientOriginalName() }}</p>
                                                <button type="button" class="text-sm text-red-500 mt-2"
                                                    wire:click="$set('driver_license_file', null)">
                                                    Remove File
                                                </button>
                                            @else
                                                @if ($employee->driverLicense)
                                                    <div class="mb-3">

                                                        <small class="text-muted">
                                                            Current file: <a
                                                                href="{{ $employee->driverLicense->file_path }}"
                                                                target="_blank" class="text-sm text-blue-500">View</a>
                                                        </small>
                                                    </div>
                                                    @if (!$keep_existing_driver_license)
                                                        <label for="driver_license_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload
                                                            </p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                (Max
                                                                10MB)</p>
                                                            <input id="driver_license_file_input" type="file"
                                                                class="hidden" wire:model="driver_license_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @else
                                                    <label for="driver_license_file_input"
                                                        class="cursor-pointer block">
                                                        <iconify-icon icon="mingcute:upload-line" width="32"
                                                            height="32"
                                                            class="text-slate-400 mx-auto"></iconify-icon>
                                                        <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                            drag
                                                            and drop</p>
                                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                            10MB)
                                                        </p>
                                                        <input id="driver_license_file_input" type="file"
                                                            class="hidden" wire:model="driver_license_file"
                                                            accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                    </label>
                                                @endif
                                            @endif
                                        </div>
                                        @error('driver_license_file')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                                @if ($employee->driverLicense)
                                    <div class="col-span-12 form-check">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer"
                                                for="keep_existing_driver_license">
                                                <input type="checkbox" class="hidden" name="checkbox"
                                                    id="keep_existing_driver_license"
                                                    wire:model.live="keep_existing_driver_license">
                                                <span
                                                    class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                    <img src="{{ asset('images/icon/ck-white.svg') }}" alt=""
                                                        class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                    existing document</span>
                                            </label>
                                        </div>

                                    </div>
                                @endif

                                <div class="from-group">
                                    <label for="driver_license_issue_date" class="form-label">Issue Date</label>
                                    <input type="date" id="driver_license_issue_date"
                                        class="form-control @error('driver_license_issue_date') !border-danger-500 @enderror"
                                        wire:model="driver_license_issue_date">
                                    @error('driver_license_issue_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="from-group">
                                    <label for="driver_license_expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" id="driver_license_expiry_date"
                                        class="form-control @error('driver_license_expiry_date') !border-danger-500 @enderror"
                                        wire:model="driver_license_expiry_date">
                                    @error('driver_license_expiry_date')
                                        <span
                                            class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="updateDriverLicense"
                                    class="btn inline-flex justify-center text-white bg-black-500">
                                    <span wire:loading.remove wire:target="updateDriverLicense">Upload</span>
                                    <iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]"
                                        wire:loading wire:target="updateDriverLicense"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Base Information Edit Modal -->
    @if ($editBaseInfoModal)
        <div>
            <div id="editBaseInfoModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editBaseInfoModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Base Information
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditBaseInfoModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text"
                                            class="form-control @error('name') !border-danger-500 @enderror"
                                            wire:model="name">
                                        @error('name')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email"
                                            class="form-control @error('email') !border-danger-500 @enderror"
                                            wire:model="email">
                                        @error('email')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text"
                                            class="form-control @error('phone') !border-danger-500 @enderror"
                                            wire:model="phone">
                                        @error('phone')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text"
                                            class="form-control @error('address') !border-danger-500 @enderror"
                                            wire:model="address">
                                        @error('address')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="nationality" class="form-label">Nationality</label>
                                        <input type="text"
                                            class="form-control @error('nationality') !border-danger-500 @enderror"
                                            wire:model="nationality">
                                        @error('nationality')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control @error('gender') !border-danger-500 @enderror"
                                            wire:model="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        @error('gender')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="birth_date" class="form-label">Birth Date</label>
                                        <input type="date"
                                            class="form-control @error('birth_date') !border-danger-500 @enderror"
                                            wire:model="birth_date">
                                        @error('birth_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employment_date" class="form-label">Employment Date</label>
                                        <input type="date"
                                            class="form-control @error('employment_date') !border-danger-500 @enderror"
                                            wire:model="employment_date">
                                        @error('employment_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditBaseInfoModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateBaseInfo" type="button" wire:target='updateBaseInfo'
                                    wire:loading.remove class="btn inline-flex justify-center btn-dark">Update</button>
                                <button wire:loading wire:target="updateBaseInfo" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    <!-- Employee Information Edit Modal -->
    @if ($editEmployeeInfoModal)
        <div>
            <div id="editEmployeeInfoModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editEmployeeInfoModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Employee Information
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditEmployeeInfoModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="insurance_office_id" class="form-label">Insurance Office</label>
                                        <select
                                            class="form-control @error('insurance_office_id') !border-danger-500 @enderror"
                                            wire:model="insurance_office_id">
                                            <option value="">Select Insurance Office</option>
                                            @foreach ($insuranceOffices as $office)
                                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('insurance_office_id')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="insurance_number" class="form-label">Insurance Number</label>
                                        <input type="text"
                                            class="form-control @error('insurance_number') !border-danger-500 @enderror"
                                            wire:model="insurance_number">
                                        @error('insurance_number')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="insurance_amount" class="form-label">Insurance Amount</label>
                                        <input type="text"
                                            class="form-control @error('insurance_amount') !border-danger-500 @enderror"
                                            wire:model="insurance_amount">
                                        @error('insurance_amount')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="academic_qualification" class="form-label">Academic
                                            Qualification</label>
                                        <input type="text"
                                            class="form-control @error('academic_qualification') !border-danger-500 @enderror"
                                            wire:model="academic_qualification">
                                        @error('academic_qualification')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="university" class="form-label">University</label>
                                        <input type="text"
                                            class="form-control @error('university') !border-danger-500 @enderror"
                                            wire:model="university">
                                        @error('university')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="graduation_year" class="form-label">Graduation Year</label>
                                        <input type="number"
                                            class="form-control @error('graduation_year') !border-danger-500 @enderror"
                                            wire:model="graduation_year">
                                        @error('graduation_year')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="military_status" class="form-label">Military Status</label>
                                        <select
                                            class="form-control @error('military_status') !border-danger-500 @enderror"
                                            wire:model="military_status">
                                            <option value="">Select Military Status</option>
                                            @foreach ($militaryStatuses as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('military_status')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="marital_status" class="form-label">Marital Status</label>
                                        <select
                                            class="form-control @error('marital_status') !border-danger-500 @enderror"
                                            wire:model="marital_status">
                                            <option value="">Select Marital Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                        @error('marital_status')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditEmployeeInfoModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateEmployeeInfo" type="button"
                                    wire:target='updateEmployeeInfo' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Update</button>
                                <button wire:loading wire:target="updateEmployeeInfo" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ID Card Modal -->
    @if ($editIdCardModal)
        <div>
            <div id="editIdCardModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editIdCardModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    ID Card Information
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditIdCardModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_file)
                                        <div class="col-span-12">

                                            <label for="id_card_file" class="form-label">ID Card Document
                                                <iconify-icon wire:loading wire:target="id_card_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($id_card_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($id_card_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $id_card_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="ID Card Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $id_card_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('id_card_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->idCard)
                                                        <div class="mb-3">

                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->idCard->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_file)
                                                            <label for="id_card_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="id_card_file_input" type="file"
                                                                    class="hidden" wire:model="id_card_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="id_card_file_input" class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="id_card_file_input" type="file"
                                                                class="hidden" wire:model="id_card_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('id_card_file')
                                                <span
                                                    class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->idCard)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_file">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_file" wire:model.live="keep_existing_file">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>

                                        </div>
                                    @endif
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
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditIdCardModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateIdCard" type="button" wire:target='updateIdCard'
                                    wire:loading.remove class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateIdCard" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Birth Certificate Modal -->
    @if ($editBirthCertificateModal)
        <div>
            <div id="editBirthCertificateModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editBirthCertificateModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Birth Certificate
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditBirthCertificateModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_birth_certificate)
                                        <div class="col-span-12">
                                            <label for="birth_certificate_file" class="form-label">Birth Certificate
                                                Document
                                                <iconify-icon wire:loading wire:target="birth_certificate_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($birth_certificate_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($birth_certificate_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $birth_certificate_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Birth Certificate Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $birth_certificate_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('birth_certificate_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->birthCertificate)
                                                        <div class="mb-3">

                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->birthCertificate->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_birth_certificate)
                                                            <label for="birth_certificate_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="birth_certificate_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="birth_certificate_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="birth_certificate_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="birth_certificate_file_input" type="file"
                                                                class="hidden" wire:model="birth_certificate_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('birth_certificate_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->birthCertificate)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_birth_certificate">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_birth_certificate"
                                                        wire:model.live="keep_existing_birth_certificate">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>

                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="birth_certificate_type" class="form-label">Certificate
                                            Type</label>
                                        <select id="birth_certificate_type" class="form-control"
                                            wire:model="birth_certificate_type">
                                            @foreach ($birthCertificateTypes as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @error('birth_certificate_type')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="birth_certificate_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('birth_certificate_issue_date') !border-danger-500 @enderror"
                                            wire:model="birth_certificate_issue_date">
                                        @error('birth_certificate_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="birth_certificate_expiry_date" class="form-label">Expiry Date (if
                                            applicable)</label>
                                        <input type="date"
                                            class="form-control @error('birth_certificate_expiry_date') !border-danger-500 @enderror"
                                            wire:model="birth_certificate_expiry_date">
                                        @error('birth_certificate_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditBirthCertificateModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateBirthCertificate" type="button"
                                    wire:target='updateBirthCertificate' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateBirthCertificate" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Army Service Paper Modal -->
    @if ($editArmyServicePaperModal)
        <div>
            <div id="editArmyServicePaperModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editArmyServicePaperModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Army Service Paper
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditArmyServicePaperModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_army_service_paper)
                                        <div class="col-span-12">
                                            <label for="army_service_paper_file" class="form-label">Army Service Paper
                                                Document
                                                <iconify-icon wire:loading wire:target="army_service_paper_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($army_service_paper_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($army_service_paper_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $army_service_paper_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Army Service Paper Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $army_service_paper_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('army_service_paper_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->armyServicePaper)
                                                        <div class="mb-3">

                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->armyServicePaper->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_army_service_paper)
                                                            <label for="army_service_paper_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="army_service_paper_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="army_service_paper_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="army_service_paper_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="army_service_paper_file_input" type="file"
                                                                class="hidden" wire:model="army_service_paper_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('army_service_paper_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->armyServicePaper)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_army_service_paper">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_army_service_paper"
                                                        wire:model.live="keep_existing_army_service_paper">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="army_service_paper_type" class="form-label">Certificate
                                            Type</label>
                                        <select id="army_service_paper_type" class="form-control"
                                            wire:model="army_service_paper_type">
                                            @foreach ($armyServicePaperTypes as $armyServicePaperType)
                                                <option value="{{ $armyServicePaperType }}">
                                                    {{ $armyServicePaperType }}</option>
                                            @endforeach
                                        </select>
                                        @error('army_service_paper_type')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="army_service_paper_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('army_service_paper_issue_date') !border-danger-500 @enderror"
                                            wire:model="army_service_paper_issue_date">
                                        @error('army_service_paper_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="army_service_paper_expiry_date" class="form-label">Expiry Date
                                            (if
                                            applicable)</label>
                                        <input type="date"
                                            class="form-control @error('army_service_paper_expiry_date') !border-danger-500 @enderror"
                                            wire:model="army_service_paper_expiry_date">
                                        @error('army_service_paper_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditArmyServicePaperModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateArmyServicePaper" type="button"
                                    wire:target='updateArmyServicePaper' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateArmyServicePaper" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Employee S1 Doc Modal -->
    @if ($editEmployeeS1DocModal)
        <div>
            <div id="editEmployeeS1DocModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editEmployeeS1DocModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Employee S1 Doc
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditEmployeeS1DocModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_employee_s1_doc)
                                        <div class="col-span-12">
                                            <label for="employee_s1_doc_file" class="form-label">Employee S1 Doc Document
                                                <iconify-icon wire:loading wire:target="employee_s1_doc_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($employee_s1_doc_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($employee_s1_doc_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $employee_s1_doc_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Employee S1 Doc Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $employee_s1_doc_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('employee_s1_doc_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->employeeS1Doc)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->employeeS1Doc->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_employee_s1_doc)
                                                            <label for="employee_s1_doc_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="employee_s1_doc_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="employee_s1_doc_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="employee_s1_doc_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="employee_s1_doc_file_input" type="file"
                                                                class="hidden" wire:model="employee_s1_doc_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('employee_s1_doc_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->employeeS1Doc)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_employee_s1_doc">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_employee_s1_doc"
                                                        wire:model.live="keep_existing_employee_s1_doc">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="s1_number" class="form-label">S1 Number</label>
                                        <input type="text" id="s1_number" class="form-control @error('s1_number') !border-danger-500 @enderror"
                                            wire:model="s1_number">
                                        @error('s1_number')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s1_doc_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('employee_s1_doc_issue_date') !border-danger-500 @enderror"
                                            wire:model="employee_s1_doc_issue_date">
                                        @error('employee_s1_doc_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s1_doc_expiry_date" class="form-label">Expiry Date
                                            (if applicable)</label>
                                        <input type="date"
                                            class="form-control @error('employee_s1_doc_expiry_date') !border-danger-500 @enderror"
                                            wire:model="employee_s1_doc_expiry_date">
                                        @error('employee_s1_doc_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditEmployeeS1DocModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateEmployeeS1Doc" type="button"
                                    wire:target='updateEmployeeS1Doc' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateEmployeeS1Doc" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Employee S2 Doc Modal -->
    @if ($editEmployeeS2DocModal)
        <div>
            <div id="editEmployeeS2DocModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editEmployeeS2DocModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Employee S2 Doc
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditEmployeeS2DocModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_employee_s2_doc)
                                        <div class="col-span-12">
                                            <label for="employee_s2_doc_file" class="form-label">Employee S2 Doc Document
                                                <iconify-icon wire:loading wire:target="employee_s2_doc_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($employee_s2_doc_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($employee_s2_doc_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $employee_s2_doc_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Employee S2 Doc Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $employee_s2_doc_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('employee_s2_doc_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    <label for="employee_s2_doc_file_input"
                                                        class="cursor-pointer block">
                                                        <iconify-icon icon="mingcute:upload-line" width="32"
                                                            height="32"
                                                            class="text-slate-400 mx-auto"></iconify-icon>
                                                        <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                            drag
                                                            and drop</p>
                                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                            10MB)
                                                        </p>
                                                        <input id="employee_s2_doc_file_input" type="file"
                                                            class="hidden" wire:model="employee_s2_doc_file"
                                                            accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                    </label>
                                                @endif
                                            </div>
                                            @error('employee_s2_doc_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="s2_amount" class="form-label">S2 Amount</label>
                                        <input type="number" step="0.01" id="s2_amount" class="form-control @error('s2_amount') !border-danger-500 @enderror"
                                            wire:model="s2_amount">
                                        @error('s2_amount')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12">
                                        <label for="s2_year" class="form-label">Year</label>
                                        <input type="number" id="s2_year" class="form-control @error('s2_year') !border-danger-500 @enderror"
                                            wire:model="s2_year">
                                        @error('s2_year')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s2_doc_issue_date" class="form-label">Issue Date</label>
                                        <input type="date"
                                            class="form-control @error('employee_s2_doc_issue_date') !border-danger-500 @enderror"
                                            wire:model="employee_s2_doc_issue_date">
                                        @error('employee_s2_doc_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s2_doc_expiry_date" class="form-label">Expiry Date (if applicable)</label>
                                        <input type="date"
                                            class="form-control @error('employee_s2_doc_expiry_date') !border-danger-500 @enderror"
                                            wire:model="employee_s2_doc_expiry_date">
                                        @error('employee_s2_doc_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditEmployeeS2DocModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateEmployeeS2Doc" type="button"
                                    wire:target='updateEmployeeS2Doc' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateEmployeeS2Doc" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Employee S6 Doc Modal -->
    @if ($editEmployeeS6DocModal)
        <div>
            <div id="editEmployeeS6DocModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editEmployeeS6DocModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Employee S6 Doc
                                </h3>
                                <button type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    wire:click="closeEditEmployeeS6DocModal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_employee_s6_doc)
                                        <div class="col-span-12">
                                            <label for="employee_s6_doc_file" class="form-label">Employee S6 Doc Document
                                                <iconify-icon wire:loading wire:target="employee_s6_doc_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($employee_s6_doc_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($employee_s6_doc_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $employee_s6_doc_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Employee S6 Doc Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $employee_s6_doc_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('employee_s6_doc_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    <label for="employee_s6_doc_file_input"
                                                        class="cursor-pointer block">
                                                        <iconify-icon icon="mingcute:upload-line" width="32"
                                                            height="32"
                                                            class="text-slate-400 mx-auto"></iconify-icon>
                                                        <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                            drag
                                                            and drop</p>
                                                        <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                            10MB)
                                                        </p>
                                                        <input id="employee_s6_doc_file_input" type="file"
                                                            class="hidden" wire:model="employee_s6_doc_file"
                                                            accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                    </label>
                                                @endif
                                            </div>
                                            @error('employee_s6_doc_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="s6_number" class="form-label">S6 Number</label>
                                        <input type="text" id="s6_number" class="form-control @error('s6_number') !border-danger-500 @enderror"
                                            wire:model="s6_number">
                                        @error('s6_number')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12">
                                        <label for="leaving_reason" class="form-label">Leaving Reason</label>
                                        <select id="leaving_reason" class="form-control"
                                            wire:model="leaving_reason">
                                            @foreach ($employeeS6DocLeavingReasons as $reason)
                                                <option value="{{ $reason }}">{{ $reason }}</option>
                                            @endforeach
                                        </select>
                                        @error('leaving_reason')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s6_doc_issue_date" class="form-label">Issue Date</label>
                                        <input type="date"
                                            class="form-control @error('employee_s6_doc_issue_date') !border-danger-500 @enderror"
                                            wire:model="employee_s6_doc_issue_date">
                                        @error('employee_s6_doc_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_s6_doc_expiry_date" class="form-label">Expiry Date (if applicable)</label>
                                        <input type="date"
                                            class="form-control @error('employee_s6_doc_expiry_date') !border-danger-500 @enderror"
                                            wire:model="employee_s6_doc_expiry_date">
                                        @error('employee_s6_doc_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditEmployeeS6DocModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateEmployeeS6Doc" type="button"
                                    wire:target='updateEmployeeS6Doc' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Upload</button>
                                <button wire:loading wire:target="updateEmployeeS6Doc" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Police Record Edit Modal -->
    @if ($editPoliceRecordModal)
        <div>
            <div id="editPoliceRecordModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editPoliceRecordModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $editing_record_id ? 'Edit' : 'Add' }} Police Record
                                </h3>
                                <button wire:click="closeEditPoliceRecordModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>

                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_police_record)
                                        <div class="col-span-12">
                                            <label for="police_record_file" class="form-label">Police Record Document
                                                <iconify-icon wire:loading wire:target="police_record_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($police_record_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($police_record_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $police_record_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Police Record Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $police_record_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('police_record_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($editing_record_id)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->policeRecords->find($editing_record_id)->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_police_record)
                                                            <label for="police_record_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    2MB)</p>
                                                                <input id="police_record_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="police_record_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="police_record_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG (Max
                                                                2MB)
                                                            </p>
                                                            <input id="police_record_file_input" type="file"
                                                                class="hidden" wire:model="police_record_file"
                                                                accept=".pdf,.jpg,.jpeg,.png">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('police_record_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($editing_record_id)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_police_record">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_police_record"
                                                        wire:model.live="keep_existing_police_record">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="police_record_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('police_record_issue_date') !border-danger-500 @enderror"
                                            wire:model="police_record_issue_date">
                                        @error('police_record_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="police_record_expiry_date" class="form-label">Expiry Date (if
                                            applicable)</label>
                                        <input type="date"
                                            class="form-control @error('police_record_expiry_date') !border-danger-500 @enderror"
                                            wire:model="police_record_expiry_date">
                                        @error('police_record_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditPoliceRecordModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updatePoliceRecord" type="button"
                                    wire:target='updatePoliceRecord' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $editing_record_id ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updatePoliceRecord" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- HR Letter Edit Modal -->
    @if ($editHrLetterModal)
        <div>
            <div id="editHrLetterModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editHrLetterModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $editing_record_id ? 'Edit' : 'Add' }} HR Letter
                                </h3>
                                <button wire:click="closeEditHrLetterModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white"
                                    data-bs-dismiss="modal">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>

                            <!-- Modal body -->
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_hr_letter)
                                        <div class="col-span-12">
                                            <label for="hr_letter_file" class="form-label">HR Letter Document
                                                <iconify-icon wire:loading wire:target="hr_letter_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($hr_letter_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($hr_letter_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill" width="48"
                                                                height="48" class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $hr_letter_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="HR Letter Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $hr_letter_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('hr_letter_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($editing_record_id)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->hrLetters->find($editing_record_id)->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_hr_letter)
                                                            <label for="hr_letter_file_input"
                                                                class="cursor-pointer block">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    2MB)</p>
                                                                <input id="hr_letter_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="hr_letter_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="hr_letter_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG (Max
                                                                2MB)
                                                            </p>
                                                            <input id="hr_letter_file_input" type="file"
                                                                class="hidden" wire:model="hr_letter_file"
                                                                accept=".pdf,.jpg,.jpeg,.png">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('hr_letter_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($editing_record_id)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_hr_letter">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_hr_letter"
                                                        wire:model.live="keep_existing_hr_letter">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0"></span>
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="hr_letter_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('hr_letter_issue_date') !border-danger-500 @enderror"
                                            wire:model="hr_letter_issue_date">
                                        @error('hr_letter_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="hr_letter_expiry_date" class="form-label">Expiry Date (if
                                            applicable)</label>
                                        <input type="date"
                                            class="form-control @error('hr_letter_expiry_date') !border-danger-500 @enderror"
                                            wire:model="hr_letter_expiry_date">
                                        @error('hr_letter_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditHrLetterModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateHrLetter" type="button"
                                    wire:target='updateHrLetter' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $editing_record_id ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updateHrLetter" type="button"
                                    class="btn inline-flex justify-center btn-dark">
                                    <span class="flex items-center">
                                        <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                            height="25"></iconify-icon>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
