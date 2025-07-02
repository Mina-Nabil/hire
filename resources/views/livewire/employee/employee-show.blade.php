<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <div>
                <h4
                    class="font-medium flex items-center lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                    <span class="flex items-center gap-2">
                        {{ $employee->name }}
                        @switch($employee->status)
                            @case('active')
                                <span
                                    class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize">{{ __('Active') }}</span>
                            @break

                            @case('suspended')
                                <span
                                    class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize">{{ __('Suspended') }}</span>
                            @break

                            @case('terminated')
                                <span
                                    class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize">{{ __('Terminated') }}</span>
                            @break

                            @case('resigned')
                                <span
                                    class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize">{{ __('Resigned') }}</span>
                            @break
                        @endswitch
                    </span>
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">Employee</p>
            </div>
        </div>
        @can('delete', $employee)
            <div class="flex items-center gap-2">
                <button
                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this employee?',color:'danger',callback:'deleteEmployee'})"
                    class="btn btn-danger">
                    <iconify-icon icon="mingcute:delete-fill" width="20" height="20"></iconify-icon>
                    Delete Employee
                </button>
                <iconify-icon wire:loading wire:target="deleteEmployee" icon="line-md:loading-twotone-loop" width="18"
                    height="18"></iconify-icon>
            </div>
        @endcan
    </div>

    <div class="grid grid-cols-12 gap-5 mt-5">

        <div class="xl:col-span-3 lg:col-span-4 col-span-12">
            <div class="card">
                <div class="card-body">

                    <!-- BEGIN: Files Card -->


                    <ul class="divide-y divide-slate-100 dark:divide-slate-700 cursor-pointer">

                        <li wire:click="changeSection('info')"
                            class="block py-[8px] p-6  {{ $section == 'info' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                            <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                {{ $this->getDocumentName('employeeInfo', 'Employee Info') }}

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
                        @if ($this->isDocActive('idCard'))
                            <li wire:click="changeSection('id_card')"
                                class="block py-[8px] p-6  {{ $section == 'id_card' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('idCard', 'ID Card') }}
                                    <span>
                                        @if ($employee->checkIDCardStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkIDCardStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkIDCardStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkIDCardStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('birthCertificate'))
                            <li wire:click="changeSection('birth_certificate')"
                                class="block py-[8px] p-6  {{ $section == 'birth_certificate' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('birthCertificate', 'Birth Certificate') }}
                                    <span>
                                        @if ($employee->checkBirthCertificateStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkBirthCertificateStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkBirthCertificateStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkBirthCertificateStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('armyServicePaper'))
                            <li wire:click="changeSection('army_service_paper')"
                                class="block py-[8px] p-6  {{ $section == 'army_service_paper' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('armyServicePaper', 'Army Service Paper') }}
                                    <span>
                                        @if ($employee->checkArmyServicePaperStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkArmyServicePaperStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkArmyServicePaperStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkArmyServicePaperStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('driverLicense'))
                            <li wire:click="changeSection('driver_license')"
                                class="block py-[8px] p-6  {{ $section == 'driver_license' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('driverLicense', 'Driver License') }}
                                    <span>
                                        @if ($employee->checkDriverLicenseStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkDriverLicenseStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkDriverLicenseStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkDriverLicenseStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('employeeContract'))
                            <li wire:click="changeSection('employee_contract')"
                                class="block py-[8px] p-6  {{ $section == 'employee_contract' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('employeeContract', 'Employee Contract') }}
                                    <span>
                                        @if ($employee->checkContractStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkContractStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkContractStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkContractStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('employeeS1Doc'))
                            <li wire:click="changeSection('employee_s1_doc')"
                                class="block py-[8px] p-6  {{ $section == 'employee_s1_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('employeeS1Doc', 'Employee S1 Doc') }}
                                    <span>

                                        @if ($employee->checkS1DocStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkS1DocStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkS1DocStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkS1DocStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('employeeS2Doc'))
                            <li wire:click="changeSection('employee_s2_doc')"
                                class="block py-[8px] p-6  {{ $section == 'employee_s2_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('employeeS2Doc', 'Employee S2 Doc') }}
                                    <span>
                                        @if ($employee->checkS2DocsStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkS2DocsStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkS2DocsStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkS2DocsStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('employeeS6Doc'))
                            <li wire:click="changeSection('employee_s6_doc')"
                                class="block py-[8px] p-6  {{ $section == 'employee_s6_doc' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('employeeS6Doc', 'Employee S6 Doc') }}
                                    <span>
                                        @if ($employee->checkS6DocsStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkS6DocsStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkS6DocsStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkS6DocsStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('policeRecord'))
                            <li wire:click="changeSection('police_record')"
                                class="block py-[8px] p-6  {{ $section == 'police_record' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('policeRecord', 'Police Record') }}
                                    <span>
                                        @if ($employee->checkPoliceRecordStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkPoliceRecordStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkPoliceRecordStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkPoliceRecordStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>
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
                        @endif

                        @if ($this->isDocActive('hrLetter'))
                            <li wire:click="changeSection('hr_letter')"
                                class="block py-[8px] p-6  {{ $section == 'hr_letter' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('hrLetter', 'HR Letter') }}
                                    <span>
                                        @if ($employee->checkHrLettersStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkHrLettersStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkHrLettersStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkHrLettersStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

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
                        @endif

                        @if ($this->isDocActive('medicalRecord'))
                            <li wire:click="changeSection('medical_record')"
                                class="block py-[8px] p-6  {{ $section == 'medical_record' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('medicalRecord', 'Medical Record') }}
                                    <span>
                                        @if ($employee->checkMedicalRecordStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkMedicalRecordStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkMedicalRecordStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkMedicalRecordStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'medical_record')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'medical_record' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('externalMedicalRecord'))
                            <li wire:click="changeSection('external_medical_record')"
                                class="block py-[8px] p-6  {{ $section == 'external_medical_record' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('externalMedicalRecord', 'External Medical Record') }}
                                    <span>
                                        @if ($employee->checkExternalMedicalRecordStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkExternalMedicalRecordStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkExternalMedicalRecordStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkExternalMedicalRecordStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'external_medical_record')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'external_medical_record' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('practiceCard'))
                            <li wire:click="changeSection('practice_card')"
                                class="block py-[8px] p-6  {{ $section == 'practice_card' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('practiceCard', 'Practice Card') }}
                                    <span>
                                        @if ($employee->checkPracticeCardStatus() === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkPracticeCardStatus() === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkPracticeCardStatus() === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkPracticeCardStatus() === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'practice_card')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'practice_card' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('skillsQualification'))
                            <li wire:click="changeSection('skills_qualification')"
                                class="block py-[8px] p-6  {{ $section == 'skills_qualification' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('skillsQualification', 'Skills Qualification') }}
                                    <span>
                                        @if ($employee->checkSkillsQualificationStatus() === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkSkillsQualificationStatus() === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkSkillsQualificationStatus() === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkSkillsQualificationStatus() === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'skills_qualification')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'skills_qualification' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('syndicateCard'))
                            <li wire:click="changeSection('syndicate_card')"
                                class="block py-[8px] p-6  {{ $section == 'syndicate_card' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('syndicateCard', 'Syndicate Card') }}
                                    <span>
                                        @if ($employee->checkSyndicateCardStatus() === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkSyndicateCardStatus() === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkSyndicateCardStatus() === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkSyndicateCardStatus() === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'syndicate_card')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'syndicate_card' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('workDeclaration'))
                            <li wire:click="changeSection('work_declaration')"
                                class="block py-[8px] p-6  {{ $section == 'work_declaration' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('workDeclaration', 'Work Declaration') }}
                                    <span>
                                        @if ($employee->checkWorkDeclarationStatus()['status'] === 'valid')
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @elseif($employee->checkWorkDeclarationStatus()['status'] === 'near_expiry')
                                            <span
                                                class="badge bg-warning-500 text-warning-500 bg-opacity-30 capitalize rounded-3xl">Near
                                                Expiry</span>
                                        @elseif($employee->checkWorkDeclarationStatus()['status'] === 'expired')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Expired</span>
                                        @elseif($employee->checkWorkDeclarationStatus()['status'] === 'missing')
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'work_declaration')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'work_declaration' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('labourDocument'))
                            <li wire:click="changeSection('labour_document')"
                                class="block py-[8px] p-6  {{ $section == 'labour_document' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('labourDocument', 'Labour Document') }}
                                    <span>
                                        @if ($employee->labourDocument)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @else
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'labour_document')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'labour_document' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('collegeCertificate'))
                            <li wire:click="changeSection('college_certificate')"
                                class="block py-[8px] p-6  {{ $section == 'college_certificate' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('collegeCertificate', 'College Certificate') }}
                                    <span>
                                        @if ($employee->collegeCertificate)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @else
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'college_certificate')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'college_certificate' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('socialPrint'))
                            <li wire:click="changeSection('social_print')"
                                class="block py-[8px] p-6  {{ $section == 'social_print' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('socialPrint', 'Social Print') }}
                                    <span>
                                        @if ($employee->socialPrint)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">Valid</span>
                                        @else
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">Missing</span>
                                        @endif
                                    </span>

                                    @if ($section == 'social_print')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'social_print' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

                        @if ($this->isDocActive('otherDocument'))
                            <li wire:click="changeSection('other_documents')"
                                class="block py-[8px] p-6  {{ $section == 'other_documents' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'hover:bg-slate-200 dark:hover:bg-slate-700 dark:text-white' }}">
                                <div class="flex justify-between space-x-2 rtl:space-x-reverse">
                                    {{ $this->getDocumentName('otherDocument', 'Other Documents') }}
                                    <span>
                                        @if ($employee->otherDocuments->count() > 0)
                                            <span
                                                class="badge bg-success-500 text-success-500 bg-opacity-30 capitalize rounded-3xl">{{ $employee->otherDocuments->count() }}</span>
                                        @else
                                            <span
                                                class="badge bg-danger-500 text-danger-500 bg-opacity-30 capitalize rounded-3xl">0</span>
                                        @endif
                                    </span>

                                    @if ($section == 'other_documents')
                                        <div class="flex-none">
                                            <button type="button"
                                                class="text-xs text-slate-900 dark:text-white {{ $section == 'other_documents' ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                <iconify-icon icon="mingcute:arrow-right-circle-fill" width="25"
                                                    height="25"></iconify-icon>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif

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

                        <div class="flex items-center gap-5">
                            <div class="dropdown relative">
                                <button class="btn inline-flex justify-center btn-dark items-center btn-sm"
                                    type="button" id="statusDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Change Status
                                    <iconify-icon icon="heroicons-outline:chevron-down"
                                        class="text-xl ltr:ml-2 rtl:mr-2"></iconify-icon>
                                </button>
                                <ul
                                    class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                                    @foreach ($statuses as $statusValue)
                                        @if ($statusValue !== $employee->status)
                                            <li>
                                                <a href="#"
                                                    wire:click.prevent="changeStatus('{{ $statusValue }}')"
                                                    class="dropdown-item text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent hover:bg-slate-100 dark:hover:bg-slate-600 dark:hover:text-white">
                                                    Set as {{ ucfirst($statusValue) }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            @can('setDocs', [$employee, 'idCard'])
                                @if (!$employee->idCard)
                                    <button type="button" class="text-slate-900 dark:text-white"
                                        wire:click="openEditBaseInfoModal">
                                        <iconify-icon icon="mingcute:edit-line" width="25"
                                            height="25"></iconify-icon>
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-12 gap-5">
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Name</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->name }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Email</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->email }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Phone</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->phone }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Address</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->address }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Mother Name</label>
                                    <div class="text-base text-slate-900 dark:text-white arabic-font">
                                        {{ $employee->mother_name }}</div>
                                </div>

                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Termination
                                        Date</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->termination_date?->format('d/m/Y') }}
                                    </div>
                                </div>


                            </div>
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Arabic Name</label>
                                    <div class="text-base text-slate-900 dark:text-white arabic-font">
                                        {{ $employee->name_ar }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Nationality</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->nationality }}
                                    </div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Gender</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->gender }}
                                    </div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Birth Date</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->birth_date?->format('d/m/Y') }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Employment
                                        Date</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
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

                        @can('setDocs', [$employee, 'employeeInfo'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditEmployeeInfoModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-12 gap-5">
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Insurance
                                        Number</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->insurance_number ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Insurance
                                        Amount</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->insurance_amount ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Academic
                                        Qualification</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->academic_qualification ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">University</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->university ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Graduation
                                        Year</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->graduation_year ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Military
                                        Status</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->military_status ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Marital
                                        Status</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->marital_status ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-5 text-wrap">
                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Employee
                                        Code</label>
                                    <div class="text-base text-slate-900 dark:text-white font-bold">
                                        {{ $employee->info?->employee_code ?? 'N/A' }}</div>
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

                        @can('setDocs', [$employee, 'idCard'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditIdCardModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
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
                                @can('setDocs', [$employee, 'idCard'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditIdCardModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload ID Card
                                    </button>
                                @endcan
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

                        @can('setDocs', [$employee, 'birthCertificate'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditBirthCertificateModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
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
                                @can('setDocs', [$employee, 'birthCertificate'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditBirthCertificateModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Birth Certificate
                                    </button>
                                @endcan
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

                        @can('setDocs', [$employee, 'armyServicePaper'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditArmyServicePaperModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
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
                                @can('setDocs', [$employee, 'armyServicePaper'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditArmyServicePaperModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Army Service Paper
                                    </button>
                                @endcan
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

                        @can('setDocs', [$employee, 'driverLicense'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditDriverLicenseModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
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
                                                $fileExt = $this->getFileExtension($employee->driverLicense->file_path);
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
                                @can('setDocs', [$employee, 'driverLicense'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditDriverLicenseModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Driver License
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'employee_contract')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Employee Contract Information
                        </h4>

                        @can('setDocs', [$employee, 'employeeContract'])
                            <button wire:click="openEditEmployeeContractModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->contracts && count($employee->contracts) > 0)
                            @foreach ($employee->contracts as $index => $contract)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">Contract - Issue Date
                                            {{ $contract->issue_date }}</h5>
                                        @can('setDocs', [$employee, 'employeeContract'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button wire:click="openEditSpecificContractModal({{ $contract->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this contract?',color:'danger',callback:'deleteEmployeeContractModal',params:{{ $contract->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($contract->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $contract->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $contract->file_path }}" alt="Employee Contract"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $contract->issue_date }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $contract->expiry_date ? $contract->expiry_date : 'N/A' }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button
                                                        wire:click="downloadEmployeeContract({{ $contract->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadEmployeeContract({{ $contract->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadEmployeeContract({{ $contract->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Employee Contracts Found</h5>
                                <p class="text-slate-500 mb-5">Please upload a contract for this employee</p>
                                @can('setDocs', [$employee, 'employeeContract'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditEmployeeContractModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Contract
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'employee_s6_doc')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Employee S6 Doc Information
                        </h4>

                        @can('setDocs', [$employee, 'employeeS6Doc'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditEmployeeS6DocModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->employeeS6Doc && count($employee->employeeS6Doc) > 0)
                            @foreach ($employee->employeeS6Doc as $index => $s6Doc)
                                <div class="card mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">S6 Document</h5>
                                        @can('setDocs', [$employee, 'employeeS6Doc'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button type="button" class="text-slate-900 dark:text-white"
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this S6 document?',color:'danger',callback:'deleteEmployeeS6DocModal',params:{{ $s6Doc->id }}})">
                                                    <iconify-icon icon="mingcute:delete-line" width="20"
                                                        height="20"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-12 gap-5 pb-5">
                                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                                <div class="mb-5 text-wrap">
                                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">S6
                                                        Number</label>
                                                    <div class="text-base text-slate-900 dark:text-white">
                                                        {{ $s6Doc->s6_number }}
                                                    </div>
                                                </div>
                                                <div class="mb-5 text-wrap">
                                                    <label
                                                        class="text-xs text-slate-500 dark:text-slate-400 m-0">Leaving
                                                        Reason</label>
                                                    <div class="text-base text-slate-900 dark:text-white">
                                                        {{ $s6Doc->leaving_reason }}
                                                    </div>
                                                </div>
                                                <div class="mb-5 text-wrap">
                                                    <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                                        Date</label>
                                                    <div class="text-base text-slate-900 dark:text-white">
                                                        {{ $s6Doc->issue_date }}
                                                    </div>
                                                </div>
                                                @if ($s6Doc->expiry_date)
                                                    <div class="mb-5 text-wrap">
                                                        <label
                                                            class="text-xs text-slate-500 dark:text-slate-400 m-0">Expiry
                                                            Date</label>
                                                        <div class="text-base text-slate-900 dark:text-white">
                                                            {{ $s6Doc->expiry_date }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                                <div class="mb-5 text-wrap">
                                                    <label
                                                        class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                                    <div class="mt-3">
                                                        @php
                                                            $fileExt = $this->getFileExtension($s6Doc->file_path);
                                                        @endphp

                                                        @if ($fileExt == 'pdf')
                                                            <!-- PDF Preview -->
                                                            <div class="border border-slate-200 rounded-md p-2">
                                                                <iframe src="{{ $s6Doc->file_path }}" width="100%"
                                                                    height="400" class="border-0"></iframe>
                                                            </div>
                                                        @else
                                                            <!-- Image Preview -->
                                                            <div class="mb-3">
                                                                <img src="{{ $s6Doc->file_path }}" alt="S6 Document"
                                                                    class="max-w-full h-auto rounded-md shadow-sm">
                                                            </div>
                                                        @endif

                                                        <!-- Download Button -->
                                                        <button
                                                            wire:click="downloadEmployeeS6Doc({{ $s6Doc->id }})"
                                                            type="button" class="btn btn-dark btn-sm mt-2"
                                                            style="min-width: 150px;">
                                                            <span class="inline-flex justify-center"
                                                                wire:loading.remove
                                                                wire:target="downloadEmployeeS6Doc({{ $s6Doc->id }})">
                                                                <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                    class="mr-1" width="18"
                                                                    height="18"></iconify-icon>
                                                                Download Document
                                                            </span>
                                                            <iconify-icon wire:loading
                                                                wire:target="downloadEmployeeS6Doc({{ $s6Doc->id }})"
                                                                icon="line-md:loading-twotone-loop" width="18"
                                                                height="18"></iconify-icon>
                                                        </button>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No S6 Documents Found</h5>
                                <p class="text-slate-500 mb-5">Please upload an S6 document for this employee</p>
                                @can('setDocs', [$employee, 'employeeS6Doc'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditEmployeeS6DocModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload S6 Document
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'police_record')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Police Record Information
                        </h4>

                        @can('setDocs', [$employee, 'policeRecord'])
                            <button wire:click="openEditPoliceRecordModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->policeRecords && $employee->policeRecords->count() > 0)
                            @foreach ($employee->policeRecords as $record)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">Police Record - Issue
                                            Date
                                            {{ $record->issue_date }}</h5>
                                        @can('setDocs', [$employee, 'policeRecord'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button
                                                    wire:click="openEditSpecificPoliceRecordModal({{ $record->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this police record?',color:'danger',callback:'deletePoliceRecordModal',params:{{ $record->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($record->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $record->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $record->file_path }}" alt="Police Record"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $record->issue_date }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $record->expiry_date ?? 'N/A' }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button wire:click="downloadPoliceRecord({{ $record->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadPoliceRecord({{ $record->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadPoliceRecord({{ $record->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Police Records Found</h5>
                                <p class="text-slate-500 mb-5">Please upload a police record for this employee</p>
                                @can('setDocs', [$employee, 'policeRecord'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditPoliceRecordModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Police Record
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'hr_letter')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            HR Letter Information
                        </h4>

                        @can('setDocs', [$employee, 'hrLetter'])
                            <button wire:click="openEditHrLetterModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->hrLetters && $employee->hrLetters->count() > 0)
                            @foreach ($employee->hrLetters as $letter)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">HR Letter - Issue Date
                                            {{ $letter->issue_date }}</h5>
                                        @can('setDocs', [$employee, 'hrLetter'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button wire:click="openEditSpecificHrLetterModal({{ $letter->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this HR letter?',color:'danger',callback:'deleteHrLetterModal',params:{{ $letter->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($letter->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $letter->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $letter->file_path }}" alt="HR Letter"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $letter->issue_date }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $letter->expiry_date ?? 'N/A' }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button wire:click="downloadHrLetter({{ $letter->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadHrLetter({{ $letter->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadHrLetter({{ $letter->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No HR Letters Found</h5>
                                <p class="text-slate-500 mb-5">Please upload an HR letter for this employee</p>
                                @can('setDocs', [$employee, 'hrLetter'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditHrLetterModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload HR Letter
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'work_declaration')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Work Declaration Information
                        </h4>

                        @can('setDocs', [$employee, 'workDeclaration'])
                            <button wire:click="openEditWorkDeclarationModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->workDeclarations && $employee->workDeclarations->count() > 0)
                            @foreach ($employee->workDeclarations as $declaration)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">Work Declaration - Issue
                                            Date
                                            {{ $declaration->issue_date }}</h5>
                                        @can('setDocs', [$employee, 'workDeclaration'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button
                                                    wire:click="openEditSpecificWorkDeclarationModal({{ $declaration->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this Work Declaration?',color:'danger',callback:'deleteWorkDeclarationModal',params:{{ $declaration->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($declaration->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $declaration->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $declaration->file_path }}" alt="Work Declaration"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $declaration->issue_date }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $declaration->expiry_date ?? 'N/A' }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button
                                                        wire:click="downloadWorkDeclaration({{ $declaration->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadWorkDeclaration({{ $declaration->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadWorkDeclaration({{ $declaration->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Work Declarations Found</h5>
                                <p class="text-slate-500 mb-5">Please upload a Work Declaration for this employee</p>
                                @can('setDocs', [$employee, 'workDeclaration'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditWorkDeclarationModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Work Declaration
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'employee_s1_doc')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Employee S1 Doc Information
                        </h4>

                        @can('setDocs', [$employee, 'employeeS1Doc'])
                            <button type="button" class="text-slate-900 dark:text-white"
                                wire:click="openEditEmployeeS1DocModal">
                                <iconify-icon icon="mingcute:edit-line" width="25" height="25"></iconify-icon>
                            </button>
                        @endcan
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
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Issue
                                            Date</label>
                                        <div class="text-base text-slate-900 dark:text-white">
                                            {{ $employee->employeeS1Doc->issue_date }}
                                        </div>
                                    </div>

                                </div>
                                <div class="xl:col-span-6 lg:col-span-6 md:col-span-6 col-span-12">
                                    <div class="mb-5 text-wrap">
                                        <label class="text-xs text-slate-500 dark:text-slate-400 m-0">Document</label>
                                        <div class="mt-3">
                                            @php
                                                $fileExt = $this->getFileExtension($employee->employeeS1Doc->file_path);
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
                                                        class="mr-1" width="18"
                                                        height="18"></iconify-icon>
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
                                @can('setDocs', [$employee, 'employeeS1Doc'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditEmployeeS1DocModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload S1 Document
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'employee_s2_doc')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Employee S2 Doc Information
                        </h4>

                        @can('setDocs', [$employee, 'employeeS2Doc'])
                            <button wire:click="openEditEmployeeS2DocModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->employeeS2Doc && count($employee->employeeS2Doc) > 0)
                            <div
                                class="py-[18px] px-6 font-normal text-sm rounded-md bg-white text-warning-500 border border-warning-500
                                    dark:bg-slate-800 mb-2">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <iconify-icon class="text-2xl flex-0"
                                        icon="heroicons:information-circle"></iconify-icon>
                                    <p class="flex-1 font-Inter">
                                        The year field must be unique. If you add a new S2 document with an existing
                                        year,
                                        it will update the existing record instead of creating a new one.
                                    </p>
                                </div>
                            </div>
                            @foreach ($employee->employeeS2Doc as $index => $s2Doc)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">S2 Doc - Year
                                            {{ $s2Doc->year }}</h5>
                                        @can('setDocs', [$employee, 'employeeS2Doc'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button wire:click="openEditSpecificS2DocModal({{ $s2Doc->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this S2 document?',color:'danger',callback:'deleteEmployeeS2DocModal',params:{{ $s2Doc->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($s2Doc->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $s2Doc->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $s2Doc->file_path }}" alt="S2 Document"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span
                                                        class="text-sm text-slate-500 dark:text-slate-400">Year:</span>
                                                    <span class="text-sm font-medium">{{ $s2Doc->year }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">S2
                                                        Amount:</span>
                                                    <span class="text-sm font-medium">{{ $s2Doc->s2_amount }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $s2Doc->issue_date }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $s2Doc->expiry_date ?? 'N/A' }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button wire:click="downloadEmployeeS2Doc({{ $s2Doc->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadEmployeeS2Doc({{ $s2Doc->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadEmployeeS2Doc({{ $s2Doc->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Employee S2 Docs Found</h5>
                                <p class="text-slate-500 mb-5">Please upload an S2 document for this employee</p>
                                @can('setDocs', [$employee, 'employeeS2Doc'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditEmployeeS2DocModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload S2 Document
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($section === 'medical_record')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Medical Record Information
                        </h4>

                        @can('setDocs', [$employee, 'medicalRecord'])
                            <button wire:click="openEditMedicalRecordModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->medicalRecord)
                            <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                    <h5 class="card-title text-slate-900 dark:text-white">Medical Record - Issue Date
                                        {{ $employee->medicalRecord->issue_date }}</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="grid grid-cols-4 gap-4">
                                        <!-- Document Preview -->
                                        <div class="col-span-3 flex justify-center items-center">
                                            @php
                                                $fileExt = $this->getFileExtension($employee->medicalRecord->file_path);
                                            @endphp

                                            @if ($fileExt == 'pdf')
                                                <div class="border border-slate-200 rounded-md p-2 w-full">
                                                    <iframe src="{{ $employee->medicalRecord->file_path }}"
                                                        width="100%" height="800" class="border-0"></iframe>
                                                </div>
                                            @else
                                                <img src="{{ $employee->medicalRecord->file_path }}"
                                                    alt="Medical Record"
                                                    class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                            @endif
                                        </div>

                                        <!-- Document Info -->
                                        <div class="col-span-1 pl-4 space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                    Date:</span>
                                                <span
                                                    class="text-sm font-medium">{{ $employee->medicalRecord->issue_date }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                    Date:</span>
                                                <span
                                                    class="text-sm font-medium">{{ $employee->medicalRecord->expiry_date ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span
                                                    class="text-sm text-slate-500 dark:text-slate-400">Status:</span>
                                                <span
                                                    class="text-sm font-medium">{{ $employee->medicalRecord->status }}</span>
                                            </div>
                                            @if ($employee->medicalRecord->insurance_number)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Insurance
                                                        Number:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $employee->medicalRecord->insurance_number }}</span>
                                                </div>
                                            @endif
                                            @if ($employee->medicalRecord->medical_card_code)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Medical
                                                        Card Code:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $employee->medicalRecord->medical_card_code }}</span>
                                                </div>
                                            @endif
                                            @if ($employee->medicalRecord->medical_card_start)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Medical
                                                        Card Start:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $employee->medicalRecord->medical_card_start }}</span>
                                                </div>
                                            @endif
                                            @if ($employee->medicalRecord->medical_card_expiry)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Medical
                                                        Card Expiry:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $employee->medicalRecord->medical_card_expiry }}</span>
                                                </div>
                                            @endif

                                            <!-- Download Button -->
                                            <div class="mt-3">
                                                <button wire:click="downloadMedicalRecord" type="button"
                                                    class="btn btn-dark btn-sm">
                                                    <span class="inline-flex items-center justify-center"
                                                        wire:loading.remove wire:target="downloadMedicalRecord">
                                                        <iconify-icon icon="fluent:arrow-download-28-filled"
                                                            class="mr-1" width="16"
                                                            height="16"></iconify-icon>
                                                        Download
                                                    </span>
                                                    <iconify-icon wire:loading wire:target="downloadMedicalRecord"
                                                        icon="line-md:loading-twotone-loop" width="16"
                                                        height="16"></iconify-icon>
                                                </button>
                                            </div>
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
                                <h5 class="text-xl font-semibold mb-4">No Medical Record Found</h5>
                                <p class="text-slate-500 mb-5">Please upload a medical record for this employee</p>
                                @can('setDocs', [$employee, 'medicalRecord'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditMedicalRecordModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Medical Record
                                    </button>
                                @endcan
                            </div>
                        @endif


                    </div>
                </div>
            @elseif ($section == 'external_medical_record')
                <!-- External Medical Record Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            External Medical Record
                        </h4>

                        @can('setDocs', [$employee, 'externalMedicalRecord'])
                            <button wire:click="openEditExternalMedicalRecordModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->externalMedicalRecord)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">External Medical Record - Issue
                                    Date
                                    {{ $employee->externalMedicalRecord->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension(
                                                $employee->externalMedicalRecord->file_path,
                                            );
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->externalMedicalRecord->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->externalMedicalRecord->file_path }}"
                                                alt="External Medical Record"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">ID Number:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->externalMedicalRecord->id_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->externalMedicalRecord->issue_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->externalMedicalRecord->expiry_date ?? 'N/A' }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadExternalMedicalRecord" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadExternalMedicalRecord">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadExternalMedicalRecord"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No External Medical Record Found</h5>
                            <p class="text-slate-500 mb-5">Please upload an external medical record for this employee
                            </p>
                            @can('setDocs', [$employee, 'externalMedicalRecord'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditExternalMedicalRecordModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload External Medical Record
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'practice_card')
                <!-- Practice Card Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Practice Card
                        </h4>

                        @can('setDocs', [$employee, 'practiceCard'])
                            <button wire:click="openEditPracticeCardModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->practiceCard)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Practice Card - Issue Date
                                    {{ $employee->practiceCard->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension($employee->practiceCard->file_path);
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->practiceCard->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->practiceCard->file_path }}"
                                                alt="Practice Card"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->practiceCard->issue_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->practiceCard->expiry_date ?? 'N/A' }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadPracticeCard" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadPracticeCard">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadPracticeCard"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No Practice Card Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a practice card for this employee</p>
                            @can('setDocs', [$employee, 'practiceCard'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditPracticeCardModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Practice Card
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'skills_qualification')
                <!-- Skills Qualification Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Skills Qualification
                        </h4>

                        @can('setDocs', [$employee, 'skillsQualification'])
                            <button wire:click="openEditSkillsQualificationModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->skillsQualifications)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Skills Qualification - Issue
                                    Date
                                    {{ $employee->skillsQualifications->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension(
                                                $employee->skillsQualifications->file_path,
                                            );
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->skillsQualifications->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->skillsQualifications->file_path }}"
                                                alt="Skills Qualification"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->skillsQualifications->issue_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->skillsQualifications->expiry_date ?? 'N/A' }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadSkillsQualification" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadSkillsQualification">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadSkillsQualification"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No Skills Qualification Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a skills qualification for this employee</p>
                            @can('setDocs', [$employee, 'skillsQualification'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditSkillsQualificationModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Skills Qualification
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'syndicate_card')
                <!-- Syndicate Card Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Syndicate Card
                        </h4>

                        @can('setDocs', [$employee, 'syndicateCard'])
                            <button wire:click="openEditSyndicateCardModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->syndicateCard)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Syndicate Card - Issue Date
                                    {{ $employee->syndicateCard->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension($employee->syndicateCard->file_path);
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->syndicateCard->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->syndicateCard->file_path }}"
                                                alt="Syndicate Card"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->syndicateCard->issue_date }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Expiry
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->syndicateCard->expiry_date ?? 'N/A' }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadSyndicateCard" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadSyndicateCard">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadSyndicateCard"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No Syndicate Card Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a syndicate card for this employee</p>
                            @can('setDocs', [$employee, 'syndicateCard'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditSyndicateCardModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Syndicate Card
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'labour_document')
                <!-- Labour Document Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Labour Document
                        </h4>

                        @can('setDocs', [$employee, 'labourDocument'])
                            <button wire:click="openEditLabourDocumentModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->labourDocument)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Labour Document - Issue Date
                                    {{ $employee->labourDocument->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension($employee->labourDocument->file_path);
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->labourDocument->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->labourDocument->file_path }}"
                                                alt="Labour Document"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->labourDocument->issue_date }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadLabourDocument" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadLabourDocument">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadLabourDocument"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No Labour Document Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a labour document for this employee</p>
                            @can('setDocs', [$employee, 'labourDocument'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditLabourDocumentModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Labour Document
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'college_certificate')
                <!-- College Certificate Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            College Certificate
                        </h4>

                        @can('setDocs', [$employee, 'collegeCertificate'])
                            <button wire:click="openEditCollegeCertificateModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->collegeCertificate)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">College Certificate - Issue Date
                                    {{ $employee->collegeCertificate->issue_date }}</h5>
                            </div>


                                <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Type: {{ $employee->collegeCertificate->type }}</h5>
                            </div>

                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension(
                                                $employee->collegeCertificate->file_path,
                                            );
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->collegeCertificate->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->collegeCertificate->file_path }}"
                                                alt="College Certificate"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->collegeCertificate->issue_date }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadCollegeCertificate" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadCollegeCertificate">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadCollegeCertificate"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No College Certificate Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a college certificate for this employee</p>
                            @can('setDocs', [$employee, 'collegeCertificate'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditCollegeCertificateModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload College Certificate
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section == 'social_print')
                <!-- Social Print Section -->
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-xl text-lg capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Social Print
                        </h4>

                        @can('setDocs', [$employee, 'socialPrint'])
                            <button wire:click="openEditSocialPrintModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                            </button>
                        @endcan
                    </div>

                    @if ($employee->socialPrint)
                        <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                            <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                <h5 class="card-title text-slate-900 dark:text-white">Social Print - Issue Date
                                    {{ $employee->socialPrint->issue_date }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Document Preview -->
                                    <div class="col-span-3 flex justify-center items-center">
                                        @php
                                            $fileExt = $this->getFileExtension($employee->socialPrint->file_path);
                                        @endphp

                                        @if ($fileExt == 'pdf')
                                            <div class="border border-slate-200 rounded-md p-2 w-full">
                                                <iframe src="{{ $employee->socialPrint->file_path }}"
                                                    width="100%" height="800" class="border-0"></iframe>
                                            </div>
                                        @else
                                            <img src="{{ $employee->socialPrint->file_path }}" alt="Social Print"
                                                class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                        @endif
                                    </div>

                                    <!-- Document Info -->
                                    <div class="col-span-1 pl-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $employee->socialPrint->issue_date }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <div class="mt-3">
                                            <button wire:click="downloadSocialPrint" type="button"
                                                class="btn btn-dark btn-sm">
                                                <span class="inline-flex items-center justify-center"
                                                    wire:loading.remove wire:target="downloadSocialPrint">
                                                    <iconify-icon icon="fluent:arrow-download-28-filled"
                                                        class="mr-1" width="16"
                                                        height="16"></iconify-icon>
                                                    Download Document
                                                </span>
                                                <iconify-icon wire:loading wire:target="downloadSocialPrint"
                                                    icon="line-md:loading-twotone-loop" width="16"
                                                    height="16"></iconify-icon>
                                            </button>
                                        </div>
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
                            <h5 class="text-xl font-semibold mb-4">No Social Print Found</h5>
                            <p class="text-slate-500 mb-5">Please upload a social print for this employee</p>
                            @can('setDocs', [$employee, 'socialPrint'])
                                <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                    wire:click="openEditSocialPrintModal">
                                    <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                        class="mr-1"></iconify-icon>
                                    Upload Social Print
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif ($section === 'other_documents')
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h4
                            class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                            Other Documents
                        </h4>

                        @can('setDocs', [$employee, 'otherDocument'])
                            <button wire:click="openEditOtherDocumentModal" class="action-btn" type="button">
                                <iconify-icon icon="heroicons:plus"></iconify-icon>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-6">
                        @if ($employee->otherDocuments->count() > 0)
                            @foreach ($employee->otherDocuments as $document)
                                <div class="card border border-slate-200 dark:border-slate-700 mb-5">
                                    <div class="card-header bg-slate-50 dark:bg-slate-700 p-3 flex justify-between">
                                        <h5 class="card-title text-slate-900 dark:text-white">{{ $document->name }}
                                            - Issue
                                            Date {{ $document->issue_date }}</h5>
                                        @can('setDocs', [$employee, 'otherDocument'])
                                            <div class="flex space-x-3 rtl:space-x-reverse">
                                                <button
                                                    wire:click="openEditSpecificOtherDocumentModal({{ $document->id }})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                </button>
                                                <button
                                                    wire:click="$dispatch('showConfirmation',{message:'Are you sure you want to delete this document?',color:'danger',callback:'deleteOtherDocumentModal',params:{{ $document->id }}})"
                                                    class="action-btn" type="button">
                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="grid grid-cols-4 gap-4">
                                            <!-- Document Preview -->
                                            <div class="col-span-3 flex justify-center items-center">
                                                @php
                                                    $fileExt = $this->getFileExtension($document->file_path);
                                                @endphp

                                                @if ($fileExt == 'pdf')
                                                    <div class="border border-slate-200 rounded-md p-2 w-full">
                                                        <iframe src="{{ $document->file_path }}" width="100%"
                                                            height="800" class="border-0"></iframe>
                                                    </div>
                                                @else
                                                    <img src="{{ $document->file_path }}"
                                                        alt="{{ $document->name }}"
                                                        class="max-h-32 max-w-full rounded-md shadow-sm object-contain">
                                                @endif
                                            </div>

                                            <!-- Document Info -->
                                            <div class="col-span-1 pl-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Document
                                                        Name:</span>
                                                    <span class="text-sm font-medium">{{ $document->name }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-slate-500 dark:text-slate-400">Issue
                                                        Date:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $document->issue_date }}</span>
                                                </div>

                                                <!-- Download Button -->
                                                <div class="mt-3">
                                                    <button wire:click="downloadOtherDocument({{ $document->id }})"
                                                        type="button" class="btn btn-dark btn-sm">
                                                        <span class="inline-flex items-center justify-center"
                                                            wire:loading.remove
                                                            wire:target="downloadOtherDocument({{ $document->id }})">
                                                            <iconify-icon icon="fluent:arrow-download-28-filled"
                                                                class="mr-1" width="16"
                                                                height="16"></iconify-icon>
                                                            Download
                                                        </span>
                                                        <iconify-icon wire:loading
                                                            wire:target="downloadOtherDocument({{ $document->id }})"
                                                            icon="line-md:loading-twotone-loop" width="16"
                                                            height="16"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-5">
                                    <iconify-icon icon="mingcute:document-line" width="60" height="60"
                                        class="text-slate-400"></iconify-icon>
                                </div>
                                <h5 class="text-xl font-semibold mb-4">No Other Documents Found</h5>
                                <p class="text-slate-500 mb-5">Please upload other documents for this employee</p>
                                @can('setDocs', [$employee, 'otherDocument'])
                                    <button type="button" class="btn btn-dark btn-sm inline-flex justify-center"
                                        wire:click="openEditOtherDocumentModal">
                                        <iconify-icon icon="lets-icons:download-circle" width="18" height="18"
                                            class="mr-1"></iconify-icon>
                                        Upload Other Document
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

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
                                                                target="_blank"
                                                                class="text-sm text-blue-500">View</a>
                                                        </small>
                                                    </div>
                                                    @if (!$keep_existing_driver_license)
                                                        <label for="driver_license_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                if ($event.dataTransfer.files.length !== 1) {
                                                                    return
                                                                }

                                                                const files = $event.dataTransfer.files

                                                                @this.upload('driver_license_file', files[0])
                                                                ">
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
                                                        class="cursor-pointer block" x-data="{
                                                            dropping: false,
                                                        }"
                                                        x-on:dragover.prevent="dropping = true"
                                                        x-on:dragleave.prevent="dropping = false"
                                                        x-on:drop="dropping = false"
                                                        x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }

                                                                    const files = $event.dataTransfer.files

                                                                    @this.upload('driver_license_file', files[0])
                                                                ">
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
                                        <label for="name_ar" class="form-label">Arabic Name</label>
                                        <input type="text"
                                            class="form-control @error('name_ar') !border-danger-500 @enderror"
                                            wire:model="name_ar">
                                        @error('name_ar')
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
                                        <label for="id_number" class="form-label">ID Number</label>
                                        <input type="text"
                                            class="form-control @error('id_number') !border-danger-500 @enderror"
                                            wire:model="id_number">
                                        @error('id_number')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="mother_name" class="form-label">Mother Name</label>
                                        <input type="text"
                                            class="form-control @error('mother_name') !border-danger-500 @enderror"
                                            wire:model="mother_name">
                                        @error('mother_name')
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

                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="termination_date" class="form-label">Termination Date</label>
                                        <input type="date"
                                            class="form-control @error('termination_date') !border-danger-500 @enderror"
                                            wire:model="termination_date">
                                        @error('termination_date')
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
                                    wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Update</button>
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
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_code" class="form-label">Employee Code</label>
                                        <input type="text"
                                            class="form-control @error('employee_code') !border-danger-500 @enderror"
                                            wire:model="employee_code">
                                        @error('employee_code')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="device_id" class="form-label">Device ID</label>
                                        <input type="text"
                                            class="form-control @error('device_id') !border-danger-500 @enderror"
                                            wire:model="device_id">
                                        @error('device_id')
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
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
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
                                                @elseif ($employee->idCard)
                                                    <div class="mb-3">

                                                        <small class="text-muted">
                                                            Current file: <a
                                                                href="{{ $employee->idCard->file_path }}"
                                                                target="_blank"
                                                                class="text-sm text-blue-500">View</a>
                                                        </small>
                                                    </div>
                                                    @if (!$keep_existing_file)
                                                        <label for="id_card_file_input" class="cursor-pointer block"
                                                            x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                            if ($event.dataTransfer.files.length !== 1) {
                                                                return
                                                            }
                                                            const files = $event.dataTransfer.files

                                                            @this.upload('id_card_file', files[0])
                                                        ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
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
                                                    <label for="id_card_file_input" class="cursor-pointer block"
                                                        x-data="{
                                                            dropping: false,
                                                        }"
                                                        x-on:dragover.prevent="dropping = true"
                                                        x-on:dragleave.prevent="dropping = false"
                                                        x-on:drop="dropping = false"
                                                        x-on:drop.prevent="
                                                                if ($event.dataTransfer.files.length !== 1) {
                                                                    return
                                                                }

                                                                const files = $event.dataTransfer.files

                                                                @this.upload('id_card_file', files[0])
                                                            ">
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
                                    <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                @enderror
                            </div>


                            @if ($employee->idCard)
                                <div class="col-span-12 form-check">
                                    <div class="checkbox-area">
                                        <label class="inline-flex items-center cursor-pointer"
                                            for="keep_existing_file">
                                            <input type="checkbox" class="hidden" name="checkbox"
                                                id="keep_existing_file" wire:model.live="keep_existing_file">
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

    @endif

    <!-- Birth Certificate Modal -->
    @if ($editBirthCertificateModal)
        <div>
            <div id="editBirthCertificateModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editBirthCertificateModalLabel" aria-hidden="true"
                wire:ignore.self>
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
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
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
                                                                class="cursor-pointer block" x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
                                                                        const files = $event.dataTransfer.files

                                                                    @this.upload('birth_certificate_file', files[0])
                                                                ">
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
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                        if ($event.dataTransfer.files.length !== 1) {
                                                                            return
                                                                        }
    
                                                                        const files = $event.dataTransfer.files
    
                                                                        @this.upload('birth_certificate_file', files[0])
                                                                    ">
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
                tabindex="-1" aria-labelledby="editArmyServicePaperModalLabel" aria-hidden="true"
                wire:ignore.self>
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
                                            <label for="army_service_paper_file" class="form-label">Army Service
                                                Paper
                                                Document
                                                <iconify-icon wire:loading wire:target="army_service_paper_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($army_service_paper_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($army_service_paper_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
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
                                                                class="cursor-pointer block" x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                            if ($event.dataTransfer.files.length !== 1) {
                                                                                return
                                                                            }
        
                                                                            const files = $event.dataTransfer.files
        
                                                                            @this.upload('army_service_paper_file', files[0])
                                                                        ">
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
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                        if ($event.dataTransfer.files.length !== 1) {
                                                                            return
                                                                        }
    
                                                                        const files = $event.dataTransfer.files
    
                                                                        @this.upload('army_service_paper_file', files[0])
                                                                    ">
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
                                        <label for="employee_s1_doc_file" class="form-label">Employee S1 Doc
                                            Document
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
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                if ($event.dataTransfer.files.length !== 1) {
                                                                    return
                                                                }
                                                                        const files = $event.dataTransfer.files

                                                                    @this.upload('employee_s1_doc_file', files[0])
                                                            ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload
                                                            </p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                (Max
                                                                10MB)</p>
                                                            <input id="employee_s1_doc_file_input" type="file"
                                                                class="hidden" wire:model="employee_s1_doc_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @else
                                                    <label for="employee_s1_doc_file_input"
                                                        class="cursor-pointer block" x-data="{
                                                            dropping: false,
                                                        }"
                                                        x-on:dragover.prevent="dropping = true"
                                                        x-on:dragleave.prevent="dropping = false"
                                                        x-on:drop="dropping = false"
                                                        x-on:drop.prevent="
                                                            if ($event.dataTransfer.files.length !== 1) {
                                                                return
                                                            }
                                                                        const files = $event.dataTransfer.files

                                                                    @this.upload('employee_s1_doc_file', files[0])
                                                        ">
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
                                    <input type="text" id="s1_number"
                                        class="form-control @error('s1_number') !border-danger-500 @enderror"
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

    @endif

    <!-- Employee S2 Doc Modal -->
    @if ($editEmployeeS2DocModal)

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
                                @if ($editing_record_id)
                                    <div class="col-span-12 form-check">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer"
                                                for="keep_existing_employee_s2_doc">
                                                <input type="checkbox" class="hidden" name="checkbox"
                                                    id="keep_existing_employee_s2_doc"
                                                    wire:model.live="keep_existing_employee_s2_doc">
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
                                @if (!$keep_existing_employee_s2_doc)
                                    <div class="col-span-12">
                                        <label for="employee_s2_doc_file" class="form-label">Employee S2 Doc
                                            Document
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
                                                <label for="employee_s2_doc_file_input" class="cursor-pointer block"
                                                    x-data="{
                                                        dropping: false,
                                                    }" x-on:dragover.prevent="dropping = true"
                                                    x-on:dragleave.prevent="dropping = false"
                                                    x-on:drop="dropping = false"
                                                    x-on:drop.prevent="
                                                                if ($event.dataTransfer.files.length !== 1) {
                                                                    return
                                                                }

                                                                const files = $event.dataTransfer.files

                                                                @this.upload('employee_s2_doc_file', files[0])
                                                            ">
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
                                    <input type="number" step="0.01" id="s2_amount"
                                        class="form-control @error('s2_amount') !border-danger-500 @enderror"
                                        wire:model="s2_amount">
                                    @error('s2_amount')
                                        <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12">
                                    <label for="s2_year" class="form-label">Year</label>
                                    <input type="number" id="s2_year"
                                        class="form-control @error('s2_year') !border-danger-500 @enderror"
                                        wire:model="s2_year">
                                    @error('s2_year')
                                        <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12 xl:col-span-6">
                                    <label for="employee_s2_doc_issue_date" class="form-label">Issue
                                        Date</label>
                                    <input type="date"
                                        class="form-control @error('employee_s2_doc_issue_date') !border-danger-500 @enderror"
                                        wire:model="employee_s2_doc_issue_date">
                                    @error('employee_s2_doc_issue_date')
                                        <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12 xl:col-span-6">
                                    <label for="employee_s2_doc_expiry_date" class="form-label">Expiry Date (if
                                        applicable)</label>
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

    @endif

    <!-- Employee S6 Doc Edit Modal -->
    @if ($editEmployeeS6DocModal)
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
                            <button wire:click="closeEditEmployeeS6DocModal" type="button"
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
                                @if (!$keep_existing_employee_s6_doc)
                                    <div class="col-span-12">
                                        <label for="employee_s6_doc_file" class="form-label">S6 Document
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
                                                            alt="S6 Document Preview">
                                                    @endif
                                                </div>
                                                <p class="text-sm text-slate-500">
                                                    {{ $employee_s6_doc_file->getClientOriginalName() }}</p>
                                                <button type="button" class="text-sm text-red-500 mt-2"
                                                    wire:click="$set('employee_s6_doc_file', null)">
                                                    Remove File
                                                </button>
                                            @else
                                                @if ($editing_record_id)
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            Current file: <a
                                                                href="{{ $employee->employeeS6Doc->find($editing_record_id)->file_path }}"
                                                                target="_blank"
                                                                class="text-sm text-blue-500">View</a>
                                                        </small>
                                                    </div>
                                                    @if (!$keep_existing_employee_s6_doc)
                                                        <label for="employee_s6_doc_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('employee_s6_doc_file', files[0])
                                                                    ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload
                                                            </p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                (Max
                                                                10MB)</p>
                                                            <input id="employee_s6_doc_file_input" type="file"
                                                                class="hidden" wire:model="employee_s6_doc_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp">
                                                        </label>
                                                    @endif
                                                @else
                                                    <label for="employee_s6_doc_file_input"
                                                        class="cursor-pointer block" x-data="{
                                                            dropping: false,
                                                        }"
                                                        x-on:dragover.prevent="dropping = true"
                                                        x-on:dragleave.prevent="dropping = false"
                                                        x-on:drop="dropping = false"
                                                        x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('employee_s6_doc_file', files[0])
                                                                    ">
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
                                                            accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp">
                                                    </label>
                                                @endif
                                            @endif
                                        </div>
                                        @error('employee_s6_doc_file')
                                            <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                                @if ($employee->employeeS6Doc)
                                    <div class="col-span-12 form-check">
                                        <div class="checkbox-area">
                                            <label class="inline-flex items-center cursor-pointer"
                                                for="keep_existing_employee_s6_doc">
                                                <input type="checkbox" class="hidden" name="checkbox"
                                                    id="keep_existing_employee_s6_doc"
                                                    wire:model.live="keep_existing_employee_s6_doc">
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
                                    <label for="s6_number" class="form-label">S6 Number</label>
                                    <input type="text" id="s6_number"
                                        class="form-control @error('s6_number') !border-danger-500 @enderror"
                                        wire:model="s6_number">
                                    @error('s6_number')
                                        <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12">
                                    <label for="leaving_reason" class="form-label">Leaving Reason</label>
                                    <select id="leaving_reason" class="form-control" wire:model="leaving_reason">
                                        @foreach ($employeeS6DocLeavingReasons as $reason)
                                            <option value="{{ $reason }}">{{ $reason }}</option>
                                        @endforeach
                                    </select>
                                    @error('leaving_reason')
                                        <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12 xl:col-span-6">
                                    <label for="employee_s6_doc_issue_date" class="form-label">Issue
                                        Date</label>
                                    <input type="date"
                                        class="form-control @error('employee_s6_doc_issue_date') !border-danger-500 @enderror"
                                        wire:model="employee_s6_doc_issue_date">
                                    @error('employee_s6_doc_issue_date')
                                        <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-span-12 xl:col-span-6">
                                    <label for="employee_s6_doc_expiry_date" class="form-label">Expiry Date (if
                                        applicable)</label>
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
                                class="btn inline-flex justify-center btn-dark">{{ $editing_record_id ? 'Update' : 'Upload' }}</button>
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
    @endif

    <!-- Police Record Edit Modal -->
    @if ($editPoliceRecordModal)
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
                                        <label for="police_record_file" class="form-label">Police Record
                                            Document
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
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('police_record_file', files[0])
                                                                    ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload
                                                            </p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                (Max
                                                                2MB)</p>
                                                            <input id="police_record_file_input" type="file"
                                                                class="hidden" wire:model="police_record_file"
                                                                accept=".pdf,.jpg,.jpeg,.png">
                                                        </label>
                                                    @endif
                                                @else
                                                    <label for="police_record_file_input"
                                                        class="cursor-pointer block" x-data="{
                                                            dropping: false,
                                                        }"
                                                        x-on:dragover.prevent="dropping = true"
                                                        x-on:dragleave.prevent="dropping = false"
                                                        x-on:drop="dropping = false"
                                                        x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('police_record_file', files[0])
                                                                    ">
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
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
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
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('hr_letter_file', files[0])
                                                                    ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    2MB)</p>
                                                                <input id="hr_letter_file_input" type="file"
                                                                    class="hidden" wire:model="hr_letter_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="hr_letter_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('hr_letter_file', files[0])
                                                                    ">
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
                                <button wire:click="updateHrLetter" type="button" wire:target='updateHrLetter'
                                    wire:loading.remove
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

    <!-- Employee Contract Edit Modal -->
    @if ($editEmployeeContractModal)
        <div>
            <div id="editEmployeeContractModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editEmployeeContractModalLabel" aria-hidden="true"
                wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $editing_contract_id ? 'Edit' : 'Add' }} Employee Contract
                                </h3>
                                <button wire:click="closeEditEmployeeContractModal" type="button"
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
                                    @if (!$keep_existing_employee_contract)
                                        <div class="col-span-12">
                                            <label for="employee_contract_file" class="form-label">Employee Contract
                                                Document
                                                <iconify-icon wire:loading wire:target="employee_contract_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($employee_contract_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($employee_contract_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $employee_contract_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Employee Contract Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $employee_contract_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('employee_contract_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($editing_contract_id)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->contracts->find($editing_contract_id)->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_employee_contract)
                                                            <label for="employee_contract_file_input"
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('employee_contract_file', files[0])
                                                                    ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="employee_contract_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="employee_contract_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="employee_contract_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('employee_contract_file', files[0])
                                                                    ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="employee_contract_file_input" type="file"
                                                                class="hidden" wire:model="employee_contract_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('employee_contract_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($editing_contract_id)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_employee_contract">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_employee_contract"
                                                        wire:model.live="keep_existing_employee_contract">
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
                                        <label for="employee_contract_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('employee_contract_issue_date') !border-danger-500 @enderror"
                                            wire:model="employee_contract_issue_date">
                                        @error('employee_contract_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="employee_contract_expiry_date" class="form-label">Expiry
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('employee_contract_expiry_date') !border-danger-500 @enderror"
                                            wire:model="employee_contract_expiry_date">
                                        @error('employee_contract_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditEmployeeContractModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateEmployeeContract" type="button"
                                    wire:target='updateEmployeeContract' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $editing_contract_id ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updateEmployeeContract" type="button"
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

    <!-- Medical Record Edit Modal -->
    @if ($showEditMedicalRecordModal)
        <div>
            <div id="editMedicalRecordModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editMedicalRecordModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Medical Record
                                </h3>
                                <button wire:click="closeEditMedicalRecordModal" type="button"
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
                                <!-- Validation Errors Summary -->
                                @if ($errors->any())
                                    <div class="p-4 mb-4 text-sm text-danger-700 bg-danger-100 rounded-lg dark:bg-danger-200 dark:text-danger-800"
                                        role="alert">
                                        <div class="font-medium">Please fix the following errors:</div>
                                        <ul class="mt-1.5 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_medical_record)
                                        <div class="col-span-12">
                                            <label for="medical_record_file" class="form-label">Medical Record
                                                Document
                                                <iconify-icon wire:loading wire:target="medical_record_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon>
                                            </label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($medical_record_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($medical_record_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $medical_record_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Medical Record Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $medical_record_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('medical_record_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->medicalRecord)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->medicalRecord->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_medical_record)
                                                            <label for="medical_record_file_input"
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('medical_record_file', files[0])
                                                                    ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="medical_record_file_input" type="file"
                                                                    class="hidden" wire:model="medical_record_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="medical_record_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('medical_record_file', files[0])
                                                                    ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="medical_record_file_input" type="file"
                                                                class="hidden" wire:model="medical_record_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('medical_record_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->medicalRecord)
                                        <div class="col-span-12">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="hidden"
                                                        wire:model.live="keep_existing_medical_record">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                    </span>
                                                    <span
                                                        class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="medical_record_issue_date" class="form-label">Issue Date</label>
                                        <input type="date" wire:model="medical_record_issue_date"
                                            class="form-control @error('medical_record_issue_date') !border-danger-500 @enderror"
                                            id="medical_record_issue_date">
                                        @error('medical_record_issue_date')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_expiry_date" class="form-label">Expiry
                                            Date</label>
                                        <input type="date" wire:model="medical_record_expiry_date"
                                            class="form-control @error('medical_record_expiry_date') !border-danger-500 @enderror"
                                            id="medical_record_expiry_date">
                                        @error('medical_record_expiry_date')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_status" class="form-label">Status</label>
                                        <select wire:model="medical_record_status"
                                            class="form-control @error('medical_record_status') !border-danger-500 @enderror"
                                            id="medical_record_status">
                                            <option value="">Select Status</option>
                                            <option value="Not Covered">Not Covered</option>
                                            <option value="Examination">Examination</option>
                                            <option value="Issuing">Issuing</option>
                                            <option value="Covered">Covered</option>
                                            <option value="External Cover">External Cover</option>
                                        </select>
                                        @error('medical_record_status')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_insurance_number" class="form-label">Insurance
                                            Number</label>
                                        <input type="text" wire:model="medical_record_insurance_number"
                                            class="form-control @error('medical_record_insurance_number') !border-danger-500 @enderror"
                                            id="medical_record_insurance_number"
                                            placeholder="Enter insurance number">
                                        @error('medical_record_insurance_number')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_medical_card_code" class="form-label">Medical
                                            Card Code</label>
                                        <input type="text" wire:model="medical_record_medical_card_code"
                                            class="form-control @error('medical_record_medical_card_code') !border-danger-500 @enderror"
                                            id="medical_record_medical_card_code"
                                            placeholder="Enter medical card code">
                                        @error('medical_record_medical_card_code')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_medical_card_start" class="form-label">Medical
                                            Card Start Date</label>
                                        <input type="date" wire:model="medical_record_medical_card_start"
                                            class="form-control @error('medical_record_medical_card_start') !border-danger-500 @enderror"
                                            id="medical_record_medical_card_start">
                                        @error('medical_record_medical_card_start')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="medical_record_medical_card_expiry" class="form-label">Medical
                                            Card Expiry Date</label>
                                        <input type="date" wire:model="medical_record_medical_card_expiry"
                                            class="form-control @error('medical_record_medical_card_expiry') !border-danger-500 @enderror"
                                            id="medical_record_medical_card_expiry">
                                        @error('medical_record_medical_card_expiry')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditMedicalRecordModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateMedicalRecord" type="button"
                                    wire:target='updateMedicalRecord' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $employee->medicalRecord ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updateMedicalRecord" type="button"
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

    <!-- External Medical Record Edit Modal -->
    @if ($showEditExternalMedicalRecordModal)
        <div>
            <div id="editExternalMedicalRecordModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editExternalMedicalRecordModalLabel" aria-hidden="true"
                wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit External Medical Record
                                </h3>
                                <button wire:click="closeEditExternalMedicalRecordModal" type="button"
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
                                <!-- Validation Errors Summary -->
                                @if ($errors->any())
                                    <div class="p-4 mb-4 text-sm text-danger-700 bg-danger-100 rounded-lg dark:bg-danger-200 dark:text-danger-800"
                                        role="alert">
                                        <div class="font-medium">Please fix the following errors:</div>
                                        <ul class="mt-1.5 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_external_medical_record)
                                        <div class="col-span-12">
                                            <label for="external_medical_record_file" class="form-label">External
                                                Medical Record
                                                Document
                                                <iconify-icon wire:loading wire:target="external_medical_record_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon>
                                            </label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($external_medical_record_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($external_medical_record_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $external_medical_record_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="External Medical Record Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $external_medical_record_file->getClientOriginalName() }}
                                                    </p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('external_medical_record_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($employee->externalMedicalRecord)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->externalMedicalRecord->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_external_medical_record)
                                                            <label for="external_medical_record_file_input"
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('external_medical_record_file', files[0])
                                                                    ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    10MB)</p>
                                                                <input id="external_medical_record_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="external_medical_record_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="external_medical_record_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('external_medical_record_file', files[0])
                                                                    ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                10MB)
                                                            </p>
                                                            <input id="external_medical_record_file_input"
                                                                type="file" class="hidden"
                                                                wire:model="external_medical_record_file"
                                                                accept=".pdf,.jpg,.jpeg,.png,.bmp,.gif">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('external_medical_record_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($employee->externalMedicalRecord)
                                        <div class="col-span-12">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="hidden"
                                                        wire:model.live="keep_existing_external_medical_record">
                                                    <span
                                                        class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                        <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                            alt=""
                                                            class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                    </span>
                                                    <span
                                                        class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                        existing document</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-span-12">
                                        <label for="external_medical_record_id_number" class="form-label">ID
                                            Number</label>
                                        <input type="text" wire:model="external_medical_record_id_number"
                                            class="form-control @error('external_medical_record_id_number') !border-danger-500 @enderror"
                                            id="external_medical_record_id_number">
                                        @error('external_medical_record_id_number')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12">
                                        <label for="external_medical_record_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date" wire:model="external_medical_record_issue_date"
                                            class="form-control @error('external_medical_record_issue_date') !border-danger-500 @enderror"
                                            id="external_medical_record_issue_date">
                                        @error('external_medical_record_issue_date')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12">
                                        <label for="external_medical_record_expiry_date" class="form-label">Expiry
                                            Date</label>
                                        <input type="date" wire:model="external_medical_record_expiry_date"
                                            class="form-control @error('external_medical_record_expiry_date') !border-danger-500 @enderror"
                                            id="external_medical_record_expiry_date">
                                        @error('external_medical_record_expiry_date')
                                            <span class="text-danger-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditExternalMedicalRecordModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateExternalMedicalRecord" type="button"
                                    wire:target='updateExternalMedicalRecord' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">Update</button>
                                <button wire:loading wire:target="updateExternalMedicalRecord" type="button"
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

    <!-- Practice Card Edit Modal -->
    @if ($showEditPracticeCardModal)
        <div>
            <div id="editPracticeCardModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editPracticeCardModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Practice Card
                                </h3>
                                <button wire:click="closeEditPracticeCardModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updatePracticeCard">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->practiceCard)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_practice_card">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_practice_card)
                                            <div class="col-span-12">
                                                <label for="practice_card_file" class="form-label">Practice Card
                                                    Document
                                                    <iconify-icon wire:loading wire:target="practice_card_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($practice_card_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($practice_card_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $practice_card_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Practice Card Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $practice_card_file->getClientOriginalName() }}</p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('practice_card_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->practiceCard)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->practiceCard->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg  text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('practice_card_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="practice_card_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="practice_card_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('practice_card_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-6">
                                            <label for="practice_card_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('practice_card_issue_date') !border-danger-500 @enderror"
                                                id="practice_card_issue_date" wire:model="practice_card_issue_date">
                                            @error('practice_card_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-span-6">
                                            <label for="practice_card_expiry_date" class="form-label">Expiry
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('practice_card_expiry_date') !border-danger-500 @enderror"
                                                id="practice_card_expiry_date"
                                                wire:model="practice_card_expiry_date">
                                            @error('practice_card_expiry_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditPracticeCardModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updatePracticeCard" type="button"
                                        wire:target='updatePracticeCard' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->practiceCard ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updatePracticeCard" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Skills Qualification Edit Modal -->
    @if ($showEditSkillsQualificationModal)
        <div>
            <div id="editSkillsQualificationModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editSkillsQualificationModalLabel" aria-hidden="true"
                wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Skills Qualification
                                </h3>
                                <button wire:click="closeEditSkillsQualificationModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updateSkillsQualification">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->skillsQualifications)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_skills_qualification">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_skills_qualification)
                                            <div class="col-span-12">
                                                <label for="skills_qualification_file" class="form-label">Skills
                                                    Qualification
                                                    Document
                                                    <iconify-icon wire:loading wire:target="skills_qualification_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($skills_qualification_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($skills_qualification_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $skills_qualification_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Skills Qualification Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $skills_qualification_file->getClientOriginalName() }}
                                                        </p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('skills_qualification_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->skillsQualifications)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->skillsQualifications->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('skills_qualification_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="skills_qualification_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="skills_qualification_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('skills_qualification_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-6">
                                            <label for="skills_qualification_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('skills_qualification_issue_date') !border-danger-500 @enderror"
                                                id="skills_qualification_issue_date"
                                                wire:model="skills_qualification_issue_date">
                                            @error('skills_qualification_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-span-6">
                                            <label for="skills_qualification_expiry_date" class="form-label">Expiry
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('skills_qualification_expiry_date') !border-danger-500 @enderror"
                                                id="skills_qualification_expiry_date"
                                                wire:model="skills_qualification_expiry_date">
                                            @error('skills_qualification_expiry_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditSkillsQualificationModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updateSkillsQualification" type="button"
                                        wire:target='updateSkillsQualification' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->skillsQualifications ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updateSkillsQualification" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Syndicate Card Edit Modal -->
    @if ($showEditSyndicateCardModal)
        <div>
            <div id="editSyndicateCardModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editSyndicateCardModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Syndicate Card
                                </h3>
                                <button wire:click="closeEditSyndicateCardModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updateSyndicateCard">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->syndicateCard)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_syndicate_card">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_syndicate_card)
                                            <div class="col-span-12">
                                                <label for="syndicate_card_file" class="form-label">Syndicate Card
                                                    Document
                                                    <iconify-icon wire:loading wire:target="syndicate_card_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($syndicate_card_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($syndicate_card_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $syndicate_card_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Syndicate Card Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $syndicate_card_file->getClientOriginalName() }}</p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('syndicate_card_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->syndicateCard)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->syndicateCard->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('syndicate_card_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="syndicate_card_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="syndicate_card_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('syndicate_card_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-6">
                                            <label for="syndicate_card_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('syndicate_card_issue_date') !border-danger-500 @enderror"
                                                id="syndicate_card_issue_date"
                                                wire:model="syndicate_card_issue_date">
                                            @error('syndicate_card_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-span-6">
                                            <label for="syndicate_card_expiry_date" class="form-label">Expiry
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('syndicate_card_expiry_date') !border-danger-500 @enderror"
                                                id="syndicate_card_expiry_date"
                                                wire:model="syndicate_card_expiry_date">
                                            @error('syndicate_card_expiry_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditSyndicateCardModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updateSyndicateCard" type="button"
                                        wire:target='updateSyndicateCard' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->syndicateCard ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updateSyndicateCard" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Work Declaration Edit Modal -->
    @if ($editWorkDeclarationModal)
        <div>
            <div id="editWorkDeclarationModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editWorkDeclarationModalLabel" aria-hidden="true"
                wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $editing_work_declaration_id ? 'Edit' : 'Add' }} Work Declaration
                                </h3>
                                <button wire:click="closeEditWorkDeclarationModal" type="button"
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
                            <div class="modal-body p-6">
                                <div class="grid grid-cols-12 gap-4">
                                    @if (!$keep_existing_work_declaration)
                                        <div class="col-span-12">
                                            <label for="work_declaration_file" class="form-label">Work Declaration
                                                Document
                                                <iconify-icon wire:loading wire:target="work_declaration_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($work_declaration_file)
                                                    <div class="flex items-center justify-center mb-3">
                                                        @if (in_array($work_declaration_file->getClientOriginalExtension(), ['pdf']))
                                                            <iconify-icon icon="mingcute:file-pdf-fill"
                                                                width="48" height="48"
                                                                class="text-red-500"></iconify-icon>
                                                        @else
                                                            <img src="{{ $work_declaration_file->temporaryUrl() }}"
                                                                class="h-40 max-w-full rounded-md object-contain"
                                                                alt="Work Declaration Preview">
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500">
                                                        {{ $work_declaration_file->getClientOriginalName() }}</p>
                                                    <button type="button" class="text-sm text-red-500 mt-2"
                                                        wire:click="$set('work_declaration_file', null)">
                                                        Remove File
                                                    </button>
                                                @else
                                                    @if ($editing_record_id)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->workDeclarations->find($editing_record_id)->file_path }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>
                                                        @if (!$keep_existing_work_declaration)
                                                            <label for="work_declaration_file_input"
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('work_declaration_file', files[0])
                                                                    ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    2MB)</p>
                                                                <input id="work_declaration_file_input"
                                                                    type="file" class="hidden"
                                                                    wire:model="work_declaration_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="work_declaration_file_input"
                                                            class="cursor-pointer block" x-data="{
                                                                dropping: false,
                                                            }"
                                                            x-on:dragover.prevent="dropping = true"
                                                            x-on:dragleave.prevent="dropping = false"
                                                            x-on:drop="dropping = false"
                                                            x-on:drop.prevent="
                                                            if ($event.dataTransfer.files.length !== 1) {
                                                                return
                                                            }

                                                            const files = $event.dataTransfer.files

                                                            @this.upload('work_declaration_file', files[0])
                                                            ">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload or
                                                                drag
                                                                and drop</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG (Max
                                                                2MB)
                                                            </p>
                                                            <input id="work_declaration_file_input" type="file"
                                                                class="hidden" wire:model="work_declaration_file"
                                                                accept=".pdf,.jpg,.jpeg,.png">
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('work_declaration_file')
                                                <span class="text-danger-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    @if ($editing_work_declaration_id)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_work_declaration">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_work_declaration"
                                                        wire:model.live="keep_existing_work_declaration">
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
                                        <label for="work_declaration_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('work_declaration_issue_date') !border-danger-500 @enderror"
                                            wire:model="work_declaration_issue_date">
                                        @error('work_declaration_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="work_declaration_expiry_date" class="form-label">Expiry
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('work_declaration_expiry_date') !border-danger-500 @enderror"
                                            wire:model="work_declaration_expiry_date">
                                        @error('work_declaration_expiry_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditWorkDeclarationModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateWorkDeclaration" type="button"
                                    wire:target='updateWorkDeclaration' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $editing_work_declaration_id ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updateWorkDeclaration" type="button"
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

    <!-- Labour Document Edit Modal -->
    @if ($showEditLabourDocumentModal)
        <div>
            <div id="editLabourDocumentModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editLabourDocumentModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Labour Document
                                </h3>
                                <button wire:click="closeEditLabourDocumentModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updateLabourDocument">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->labourDocument)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_labour_document">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_labour_document)
                                            <div class="col-span-12">
                                                <label for="labour_document_file" class="form-label">Labour Document
                                                    <iconify-icon wire:loading wire:target="labour_document_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($labour_document_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($labour_document_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $labour_document_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Labour Document Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $labour_document_file->getClientOriginalName() }}
                                                        </p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('labour_document_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->labourDocument)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->labourDocument->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('labour_document_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="labour_document_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="labour_document_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('labour_document_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-12">
                                            <label for="labour_document_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('labour_document_issue_date') !border-danger-500 @enderror"
                                                id="labour_document_issue_date"
                                                wire:model="labour_document_issue_date">
                                            @error('labour_document_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-span-12">
                                            <label for="labour_document_registration_date" class="form-label">Registration
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('labour_document_registration_date') !border-danger-500 @enderror"
                                                id="labour_document_registration_date"
                                                wire:model="labour_document_registration_date">
                                            @error('labour_document_registration_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditLabourDocumentModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updateLabourDocument" type="button"
                                        wire:target='updateLabourDocument' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->labourDocument ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updateLabourDocument" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- College Certificate Edit Modal -->
    @if ($showEditCollegeCertificateModal)
        <div>
            <div id="editCollegeCertificateModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editCollegeCertificateModalLabel" aria-hidden="true"
                wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit College Certificate
                                </h3>
                                <button wire:click="closeEditCollegeCertificateModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updateCollegeCertificate">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->collegeCertificate)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_college_certificate">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_college_certificate)
                                            <div class="col-span-12">
                                                <label for="college_certificate_file" class="form-label">College
                                                    Certificate
                                                    <iconify-icon wire:loading wire:target="college_certificate_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($college_certificate_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($college_certificate_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $college_certificate_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="College Certificate Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $college_certificate_file->getClientOriginalName() }}
                                                        </p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('college_certificate_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->collegeCertificate)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->collegeCertificate->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('college_certificate_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="college_certificate_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="college_certificate_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('college_certificate_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-12">
                                            <label for="college_certificate_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('college_certificate_issue_date') !border-danger-500 @enderror"
                                                id="college_certificate_issue_date"
                                                wire:model="college_certificate_issue_date">
                                            @error('college_certificate_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-span-12">
                                            <label for="college_certificate_type" class="form-label">Type</label>
                                            <select name="college_certificate_type" id="college_certificate_type"
                                                class="form-control @error('college_certificate_type') !border-danger-500 @enderror"
                                                wire:model="college_certificate_type">
                                                <option value="">Select Type</option>
                                                @foreach ($college_certificate_types as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('college_certificate_type')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditCollegeCertificateModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updateCollegeCertificate" type="button"
                                        wire:target='updateCollegeCertificate' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->collegeCertificate ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updateCollegeCertificate" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Social Print Edit Modal -->
    @if ($showEditSocialPrintModal)
        <div>
            <div id="editSocialPrintModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editSocialPrintModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    Edit Social Print
                                </h3>
                                <button wire:click="closeEditSocialPrintModal" type="button"
                                    class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-slate-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <form wire:submit.prevent="updateSocialPrint">
                                <!-- Modal body -->
                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-12 gap-4">
                                        @if ($employee->socialPrint)
                                            <div class="col-span-12">
                                                <div class="checkbox-area">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" class="hidden"
                                                            wire:model.live="keep_existing_social_print">
                                                        <span
                                                            class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                                                            <img src="{{ asset('images/icon/ck-white.svg') }}"
                                                                alt=""
                                                                class="h-[10px] w-[10px] block m-auto opacity-0" />
                                                        </span>
                                                        <span
                                                            class="text-slate-600 dark:text-slate-300 text-sm leading-6">Keep
                                                            existing document</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$keep_existing_social_print)
                                            <div class="col-span-12">
                                                <label for="social_print_file" class="form-label">Social Print
                                                    <iconify-icon wire:loading wire:target="social_print_file"
                                                        icon="line-md:loading-twotone-loop" width="18"
                                                        height="18"></iconify-icon>
                                                </label>
                                                <div
                                                    class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                    @if ($social_print_file)
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($social_print_file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $social_print_file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Social Print Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $social_print_file->getClientOriginalName() }}
                                                        </p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('social_print_file', null)">
                                                            Remove File
                                                        </button>
                                                    @else
                                                        @if ($employee->socialPrint)
                                                            <div class="mb-3">
                                                                <small class="text-muted">
                                                                    Current file: <a
                                                                        href="{{ $employee->socialPrint->file_path }}"
                                                                        target="_blank"
                                                                        class="text-sm text-blue-500">View</a>
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center justify-center">
                                                            <label
                                                                class="cursor-pointer flex flex-col items-center justify-center w-full h-40 rounded-lg text-slate-500 hover:border-primary-500 transition-colors duration-150"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                    if ($event.dataTransfer.files.length !== 1) {
                                                                        return
                                                                    }
    
                                                                    const files = $event.dataTransfer.files
    
                                                                    @this.upload('social_print_file', files[0])
                                                                    ">
                                                                <div
                                                                    class="flex flex-col items-center justify-center">
                                                                    <iconify-icon
                                                                        icon="heroicons:cloud-arrow-up-solid"
                                                                        class="text-slate-500 text-2xl"></iconify-icon>
                                                                    <p class="mt-2 text-sm">Click to upload or drag
                                                                        and drop</p>
                                                                    <p class="text-xs mt-1">PDF, JPG, PNG (max. 10MB)
                                                                    </p>
                                                                </div>
                                                                <input id="social_print_file" type="file"
                                                                    class="hidden" accept="image/*,.pdf"
                                                                    wire:model.live="social_print_file">
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('social_print_file')
                                                    <span class="text-danger-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-span-12">
                                            <label for="social_print_issue_date" class="form-label">Issue
                                                Date</label>
                                            <input type="date"
                                                class="form-control @error('social_print_issue_date') !border-danger-500 @enderror"
                                                id="social_print_issue_date" wire:model="social_print_issue_date">
                                            @error('social_print_issue_date')
                                                <span class="text-danger-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                    <button wire:click="closeEditSocialPrintModal" type="button"
                                        class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                    <button wire:click="updateSocialPrint" type="button"
                                        wire:target='updateSocialPrint' wire:loading.remove
                                        class="btn inline-flex justify-center btn-dark">{{ $employee->socialPrint ? 'Update' : 'Upload' }}</button>
                                    <button wire:loading wire:target="updateSocialPrint" type="button"
                                        class="btn inline-flex justify-center btn-dark">
                                        <span class="flex items-center">
                                            <iconify-icon icon="line-md:loading-twotone-loop" width="25"
                                                height="25"></iconify-icon>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Other Document Edit Modal -->
    @if ($editOtherDocumentModal)
        <div>
            <div id="editOtherDocumentModal"
                class="modal fade fixed top-0 left-0 show w-full h-full outline-none overflow-x-hidden overflow-y-auto"
                tabindex="-1" aria-labelledby="editOtherDocumentModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog relative w-auto pointer-events-none">
                    <div
                        class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                        <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
                                <h3 class="text-xl font-medium text-white dark:text-white capitalize">
                                    {{ $editing_other_document_id ? 'Edit' : 'Add' }} Other Document
                                </h3>
                                <button wire:click="closeEditOtherDocumentModal" type="button"
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
                            <div class="modal-body p-6">
                                <div class="grid grid-cols-12 gap-4">
                                    @if ($editing_other_document_id)
                                        <div class="col-span-12 form-check">
                                            <div class="checkbox-area">
                                                <label class="inline-flex items-center cursor-pointer"
                                                    for="keep_existing_other_document">
                                                    <input type="checkbox" class="hidden" name="checkbox"
                                                        id="keep_existing_other_document"
                                                        wire:model.live="keep_existing_other_document">
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
                                        <label for="other_document_name" class="form-label">Document Name</label>
                                        <input type="text"
                                            class="form-control @error('other_document_name') !border-danger-500 @enderror"
                                            wire:model="other_document_name" placeholder="Enter document name">
                                        @error('other_document_name')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if (!$keep_existing_other_document)
                                        <div class="col-span-12">
                                            <label for="other_document_file" class="form-label">Other Document
                                                <iconify-icon wire:loading wire:target="other_document_file"
                                                    icon="line-md:loading-twotone-loop" width="18"
                                                    height="18"></iconify-icon></label>
                                            <div
                                                class="border-2 border-dashed border-slate-200 rounded-md p-4 text-center">
                                                @if ($other_document_file)
                                                    @foreach ($other_document_file as $key => $file)
                                                    @if(!$file) @continue @endif
                                                        <div class="flex items-center justify-center mb-3">
                                                            @if (in_array($file->getClientOriginalExtension(), ['pdf']))
                                                                <iconify-icon icon="mingcute:file-pdf-fill"
                                                                    width="48" height="48"
                                                                    class="text-red-500"></iconify-icon>
                                                            @else
                                                                <img src="{{ $file->temporaryUrl() }}"
                                                                    class="h-40 max-w-full rounded-md object-contain"
                                                                    alt="Other Document Preview">
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-slate-500">
                                                            {{ $file->getClientOriginalName() }}</p>
                                                        <button type="button" class="text-sm text-red-500 mt-2"
                                                            wire:click="$set('other_document_file.{{ $key }}', null)">
                                                            Remove File
                                                        </button>
                                                    @endforeach
                                                @else
                                                    @if ($editing_other_document_id)
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                Current file: <a
                                                                    href="{{ $employee->otherDocuments->find($editing_other_document_id)->file_path ?? '#' }}"
                                                                    target="_blank"
                                                                    class="text-sm text-blue-500">View</a>
                                                            </small>
                                                        </div>

                                                        @if (!$keep_existing_other_document)
                                                            <label for="other_document_file_input"
                                                                class="cursor-pointer block"
                                                                x-data="{
                                                                    dropping: false,
                                                                }"
                                                                x-on:dragover.prevent="dropping = true"
                                                                x-on:dragleave.prevent="dropping = false"
                                                                x-on:drop="dropping = false"
                                                                x-on:drop.prevent="
                                                                if ($event.dataTransfer.files.length !== 1) {
                                                                    return
                                                                }

                                                                const files = $event.dataTransfer.files

                                                                @this.upload('other_document_file', files[0])
                                                                ">
                                                                <iconify-icon icon="mingcute:upload-line"
                                                                    width="32" height="32"
                                                                    class="text-slate-400 mx-auto"></iconify-icon>
                                                                <p class="mt-2 text-sm text-slate-500">Click to upload
                                                                </p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF
                                                                    (Max
                                                                    2MB)</p>
                                                                <input id="other_document_file_input" type="file"
                                                                    class="hidden" wire:model="other_document_file"
                                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                            </label>
                                                        @endif
                                                    @else
                                                        <label for="other_document_file_input"
                                                            class="cursor-pointer block">
                                                            <iconify-icon icon="mingcute:upload-line" width="32"
                                                                height="32"
                                                                class="text-slate-400 mx-auto"></iconify-icon>
                                                            <p class="mt-2 text-sm text-slate-500">Click to upload</p>
                                                            <p class="text-xs text-slate-400">PDF, JPG, PNG, GIF (Max
                                                                2MB)
                                                            </p>
                                                            <input id="other_document_file_input" type="file"
                                                                class="hidden" wire:model="other_document_file"
                                                                accept=".pdf,.jpg,.jpeg,.png" multiple>
                                                        </label>
                                                    @endif
                                                @endif
                                            </div>
                                            @error('other_document_file')
                                                <span
                                                    class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="col-span-12">
                                        <label for="other_document_issue_date" class="form-label">Issue
                                            Date</label>
                                        <input type="date"
                                            class="form-control @error('other_document_issue_date') !border-danger-500 @enderror"
                                            wire:model="other_document_issue_date">
                                        @error('other_document_issue_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                                <button wire:click="closeEditOtherDocumentModal" type="button"
                                    class="btn inline-flex justify-center btn-outline-dark">Cancel</button>
                                <button wire:click="updateOtherDocument" type="button"
                                    wire:target='updateOtherDocument' wire:loading.remove
                                    class="btn inline-flex justify-center btn-dark">{{ $editing_other_document_id ? 'Update' : 'Upload' }}</button>
                                <button wire:loading wire:target="updateOtherDocument" type="button"
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
