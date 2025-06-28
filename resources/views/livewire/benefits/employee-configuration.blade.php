<div class="space-y-5 profile-page mx-auto">
    <div class="flex justify-between">
        <div class="flex gap-5">
            <h4>
                <b>{{ $employee->name }}</b>
            </h4>
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class="dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">
                    <li wire:click="editConfiguration()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Edit Compensation & Benefits
                    </li>
                    <li wire:click="editVacations()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Edit Vacation Package
                    </li>
                    <li wire:click="editAttendance()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Edit Attendance Rules
                    </li>
                    <li wire:click="addCustomBaseBenefit()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Add Custom Base Compensation or Benefit
                    </li>
                    <li wire:click="addCustomVacationBenefit()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Add Custom Vacation Rule
                    </li>
                    <li wire:click="addLoan()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Add Loan
                    </li>
                    <li wire:click="addPurchase()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Add Purchase
                    </li>
                    <li wire:click="addExtraPayment()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Add Extra Payment
                    </li>
                    <li wire:click="openEditBaseInfoModal()"
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                                dark:hover:text-white cursor-pointer">
                        Edit Employee Base Info
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card-body flex flex-col col-span-2" wire:ignore>
        <div class="card-text h-full">
            <div class="flex">
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('info')">
                        <a href="#tabs-info"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'info') active @endif dark:text-slate-300"
                            id="tabs-info-tab" data-bs-toggle="pill" data-bs-target="#tabs-info" role="tab"
                            aria-controls="tabs-info" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:information-outline"></iconify-icon>
                            Employee & Benefits</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('payrolls')">
                        <a href="#tabs-payrolls"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'payrolls') active @endif dark:text-slate-300"
                            id="tabs-payrolls-tab" data-bs-toggle="pill" data-bs-target="#tabs-payrolls" role="tab"
                            aria-controls="tabs-payrolls" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:currency-usd"></iconify-icon>
                            Payrolls</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('payments')">
                        <a href="#tabs-payments"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'payments') active @endif dark:text-slate-300"
                            id="tabs-payments-tab" data-bs-toggle="pill" data-bs-target="#tabs-payments" role="tab"
                            aria-controls="tabs-payments" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:currency-usd"></iconify-icon>
                            Payments</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('vacations')">
                        <a href="#tabs-vacations"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'vacations') active @endif dark:text-slate-300"
                            id="tabs-vacations-tab" data-bs-toggle="pill" data-bs-target="#tabs-vacations"
                            role="tab" aria-controls="tabs-vacations" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:beach"></iconify-icon>
                            Vacations</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="setActiveTab('loans')">
                        <a href="#tabs-loans"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($activeTab === 'loans') active @endif dark:text-slate-300"
                            id="tabs-loans-tab" data-bs-toggle="pill" data-bs-target="#tabs-loans" role="tab"
                            aria-controls="tabs-loans" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:cash-multiple"></iconify-icon>
                            Loans & Purchases</a>
                    </li>
                </ul>
                <div>
                    <h4>
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading
                            wire:target="setActiveTab" icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="tabs-tabContent">
        <!-- Employee Info & Benefits Tab -->
        <div class="tab-pane fade @if ($activeTab === 'info') show active @endif" id="tabs-info"
            role="tabpanel" aria-labelledby="tabs-info-tab">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <!-- Employee Info Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Employee Information</h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Name:</div>
                                    <div class="col-md-8">{{ $employee->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Email:</div>
                                    <div class="col-md-8">{{ $employee->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Phone:</div>
                                    <div class="col-md-8">{{ $employee->phone }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Gender:</div>
                                    <div class="col-md-8">{{ $employee->gender }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Nationality:</div>
                                    <div class="col-md-8">{{ $employee->nationality }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Birth Date:</div>
                                    <div class="col-md-8">
                                        {{ $employee->birth_date ? $employee->birth_date->format('d/m/Y') : '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Employment Date:</div>
                                    <div class="col-md-8">
                                        {{ $employee->employment_date ? $employee->employment_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Address:</div>
                                    <div class="col-md-8">{{ $employee->address }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">ID Number:</div>
                                    <div class="col-md-8">{{ $employee->id_number }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Termination Date:</div>
                                    <div class="col-md-8">
                                        {{ $employee->termination_date ? $employee->termination_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Release Date:</div>
                                    <div class="col-md-8">
                                        {{ $employee->release_date ? $employee->release_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 font-weight-bold">Absent Date:</div>
                                    <div class="col-md-8">
                                        {{ $employee->absent_date ? $employee->absent_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Base Benefits Card -->
                    <div class="col-md-6 mt-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Base Benefits</h4>
                            </div>
                            <div class="card-body p-4">
                                @if (count($employeeBenefits) > 0)
                                    <div class="space-y-3">
                                        @foreach ($employeeBenefits as $benefit)
                                            <div class="border rounded-lg p-3 hover:bg-slate-50">
                                                <div class="flex justify-between items-center">
                                                    <h6 class="font-medium text-slate-900">{{ $benefit->name }}</h6>
                                                    @if ($benefit->end_date)
                                                        <span class="badge bg-danger">Ended:
                                                            {{ $benefit->end_date->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </div>
                                                <div class="grid grid-cols-2 gap-1 mt-2 text-sm">
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Type:</span>
                                                        <span>{{ $this->getBenefitTypeLabel($benefit->type) }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Amount:</span>
                                                        <span class="font-medium">{{ $benefit->amount }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Paid to:</span>
                                                        <span>{{ $this->getReceiverLabel($benefit->receiver) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        No base benefits found for this employee.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div>


                    <!-- Benefit Configuration Card -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Benefit Configuration</h4>
                            </div>
                            <div class="card-body p-4">
                                @if ($employee->benefitConfiguration)
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Package:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->salaryGrade->name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Manager:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->manager->name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Attendance Calculation:</div>
                                        <div class="col-md-7">
                                            {{ $this->getAttendanceCalculationLabel($employee->benefitConfiguration->attendance_calculation) }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Working Day Start:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->working_day_start_min }}
                                            - {{ $employee->benefitConfiguration->working_day_start_max }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Working Day End:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->working_day_end_min }} -
                                            {{ $employee->benefitConfiguration->working_day_end_max }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Daily Working Hours:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->daily_working_hours }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Overtime Rate:</div>
                                        <div class="col-md-7">{{ $employee->benefitConfiguration->overtime_rate }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Is Automatic Overtime:</div>
                                        <div class="col-md-7">
                                            {{ $employee->benefitConfiguration->is_automatic_overtime ? 'Yes' : 'No' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Bus:</div>
                                        <div class="col-md-7">
                                            {{ ucfirst($employee->benefitConfiguration->bus?->name ?? '-') }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Working Days:</div>
                                        <div class="col-md-7">
                                            @foreach ($employee->workingDays as $day)
                                                {{ ucfirst($day->type) }} @if (!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        No benefit configuration found for this employee.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    <!-- Vacation Benefits Card -->
                    <div class="col-md-6 mt-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Vacation Benefits</h4>
                            </div>
                            <div class="card-body p-4">
                                @if (count($employeeVacations) > 0)
                                    <div class="space-y-3">
                                        @foreach ($employeeVacations as $vacation)
                                            <div class="border rounded-lg p-3 hover:bg-slate-50">
                                                <div class="flex justify-between items-center">
                                                    <h6 class="font-medium text-slate-900">{{ $vacation->name }}</h6>
                                                    @if ($vacation->end_date)
                                                        <span class="badge bg-danger">Ended:
                                                            {{ $vacation->end_date->format('d/m/Y') }}</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </div>
                                                <div class="grid grid-cols-2 gap-1 mt-2 text-sm">
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Type:</span>
                                                        <span>{{ $this->getBenefitTypeLabel($vacation->type) }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Increment Rate:</span>
                                                        <span class="font-medium">{{ $vacation->inc_rate }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Hour Price:</span>
                                                        <span>{{ $vacation->hour_price }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Balance:</span>
                                                        <span>{{ $vacation->current_balance }}/{{ $vacation->max_balance }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-slate-500 mr-2">Apply Deadline:</span>
                                                        <span>{{ $vacation->apply_deadline . ' days' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        No vacation benefits found for this employee.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Payments Tab -->
        <div class="tab-pane fade @if ($activeTab === 'payments') show active @endif" id="tabs-payments"
            role="tabpanel" aria-labelledby="tabs-payments-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Employee Payments</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($employeePayments->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        <th scope="col" class="table-th">Date</th>
                                                        <th scope="col" class="table-th">Benefit</th>
                                                        <th scope="col" class="table-th">Amount</th>
                                                        <th scope="col" class="table-th">Status</th>
                                                        <th scope="col" class="table-th">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($employeePayments as $payment)
                                                        <tr>
                                                            <td class="table-td">
                                                                {{ $payment->created_at->format('d/m/Y') }}</td>
                                                            <td class="table-td">
                                                                {{ $payment->baseBenefit->name ?? '-' }}</td>
                                                            <td class="table-td">{{ $payment->amount }}</td>
                                                            <td class="table-td">
                                                                @if ($payment->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($payment->status == 'approved')
                                                                    <span class="badge bg-info">Approved</span>
                                                                @elseif($payment->status == 'paid')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($payment->status == 'rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </td>
                                                            <td class="table-td">{{ $payment->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $employeePayments->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No payments found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Extra Payments</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($extraPayments->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        <th scope="col" class="table-th">Date</th>
                                                        <th scope="col" class="table-th">Amount</th>
                                                        <th scope="col" class="table-th">For</th>
                                                        <th scope="col" class="table-th">Status</th>
                                                        <th scope="col" class="table-th">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($extraPayments as $payment)
                                                        <tr>
                                                            <td class="table-td">
                                                                {{ $payment->created_at->format('d/m/Y') }}</td>
                                                            <td class="table-td">{{ $payment->amount }}</td>
                                                            <td class="table-td">
                                                                @if ($payment->payable_id)
                                                                    {{ $payment->payable_type }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="table-td">
                                                                @if ($payment->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($payment->status == 'approved')
                                                                    <span class="badge bg-info">Approved</span>
                                                                @elseif($payment->status == 'paid')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($payment->status == 'rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </td>
                                                            <td class="table-td">{{ $payment->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $extraPayments->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No payments found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payrolls Tab -->
        <div class="tab-pane fade @if ($activeTab === 'payrolls') show active @endif" id="tabs-payrolls"
            role="tabpanel" aria-labelledby="tabs-payrolls-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Employee Payments</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 no-wrap">
                                    <thead>
                                        <tr>
                                            <th class="table-th">Gross Salary</th>
                                            <th class="table-th">Social Insurance Salary</th>
                                            <th class="table-th">Other Amount</th>
                                            <th class="table-th">Penalties</th>
                                            <th class="table-th">Extra Payments</th>
                                            <th class="table-th">Overtime</th>
                                            <th class="table-th">Adjustment</th>
                                            <th class="table-th">Net Amount</th>
                                            <th class="table-th">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                        @forelse($payrollRecords as $payrollRecord)
                                            <tr wire:key="employee-{{ $payrollRecord->id }}">
                                                <td class="table-td">
                                                    {{ number_format($payrollRecord->gross_salary, 2) }}</td>
                                                <td class="table-td">
                                                    {{ number_format($payrollRecord->insurance_amount, 2) }}
                                                </td>
                                                <td class="table-td">
                                                    {{ number_format($payrollRecord->other_amount, 2) }}</td>
                                                <td class="table-td">
                                                    <div class="flex flex-col">
                                                        <span>{{ number_format($payrollRecord->penalties_days, 2) }}
                                                            days</span>
                                                        <span
                                                            class="text-xs text-danger-500">-{{ number_format($payrollRecord->penalties_amount, 2) }}
                                                            <small>EGP</small></span>
                                                    </div>
                                                </td>
                                                <td class="table-td">
                                                    {{ number_format($payrollRecord->extra_payments, 2) }}</td>
                                                <td class="table-td">
                                                    <div class="flex flex-col">
                                                        <span>{{ number_format($payrollRecord->overtime_hours, 2) }}
                                                            hours</span>
                                                        <span
                                                            class="text-xs text-success-500">+{{ number_format($payrollRecord->overtime_amount, 2) }}
                                                            <small>EGP</small></span>
                                                    </div>
                                                </td>
                                                <td class="table-td">
                                                    <div class="flex flex-col">
                                                        <span>{{ number_format($payrollRecord->adj_amount, 2) }}
                                                            EGP</span>
                                                        @if (!empty($payrollRecord->adj_desc))
                                                            <span
                                                                class="text-xs text-slate-500">{{ Str::limit($payrollRecord->adj_desc, 30) }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="table-td font-semibold">
                                                    {{ number_format($payrollRecord->net_after_deductions, 2) }}</td>
                                                <td class="table-td">
                                                    <div class="flex space-x-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            wire:click="showEmployeeDetails({{ $payrollRecord->id }})">
                                                            <span class="flex items-center">
                                                                <iconify-icon icon="heroicons-outline:eye"
                                                                    class="text-base ltr:mr-1 rtl:ml-1"></iconify-icon>
                                                                Details
                                                            </span>
                                                        </button>

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="table-td text-center py-8">
                                                    <div class="flex flex-col items-center">
                                                        <iconify-icon icon="heroicons-outline:document-text"
                                                            class="text-5xl text-slate-400 mb-2"></iconify-icon>
                                                        <h5 class="text-xl font-medium text-slate-400">No payroll
                                                            records found
                                                        </h5>
                                                        <p class="text-sm text-slate-400 mt-1">This employee doesn't
                                                            have any
                                                            payroll records</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if (isset($payrollRecords))
                                <div class="mt-6">
                                    {{ $payrollRecords->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacations Tab -->
        <div class="tab-pane fade @if ($activeTab === 'vacations') show active @endif" id="tabs-vacations"
            role="tabpanel" aria-labelledby="tabs-vacations-tab">
            <div class="row">
                <!-- Applied Vacations -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Applied Vacations</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($appliedVacations->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        {{-- <th scope="col" class="table-th">Date</th> --}}
                                                        <th scope="col" class="table-th">Type</th>
                                                        <th scope="col" class="table-th">Hours</th>
                                                        <th scope="col" class="table-th">Status</th>
                                                        <th scope="col" class="table-th">New Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($appliedVacations as $vacation)
                                                        <tr>
                                                            {{-- <td class="table-td">{{ $vacation->created_at->format('d/m/Y') }}</td> --}}
                                                            <td class="table-td">
                                                                {{ $vacation->vacationBenefit->name ?? '-' }}</td>
                                                            <td class="table-td">{{ $vacation->hours }}</td>
                                                            <td class="table-td">
                                                                @if ($vacation->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($vacation->status == 'approved')
                                                                    <span class="badge bg-success">Approved</span>
                                                                @elseif($vacation->status == 'rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </td>
                                                            <td class="table-td">{{ $vacation->new_balance }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $appliedVacations->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No applied vacations found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gained Vacations -->
                <div class="col-md-6 mt-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Gained Vacations</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($gainedVacations->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        <th scope="col" class="table-th">Date</th>
                                                        <th scope="col" class="table-th">Type</th>
                                                        <th scope="col" class="table-th">Days</th>
                                                        <th scope="col" class="table-th">New Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($gainedVacations as $vacation)
                                                        <tr>
                                                            <td class="table-td">
                                                                {{ $vacation->created_at->format('d/m/Y') }}</td>
                                                            <td class="table-td">
                                                                {{ $vacation->vacationBenefit->name ?? '-' }}</td>
                                                            <td class="table-td">{{ $vacation->days }}</td>
                                                            <td class="table-td">{{ $vacation->new_balance }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $gainedVacations->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No gained vacations found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loans & Purchases Tab -->
        <div class="tab-pane fade @if ($activeTab === 'loans') show active @endif" id="tabs-loans"
            role="tabpanel" aria-labelledby="tabs-loans-tab">
            <div class="row">
                <!-- Loans -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Loans</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($loans->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        <th scope="col" class="table-th">Date</th>
                                                        <th scope="col" class="table-th">Amount</th>
                                                        <th scope="col" class="table-th">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($loans as $loan)
                                                        <tr>
                                                            <td class="table-td">
                                                                {{ $loan->created_at->format('d/m/Y') }}</td>
                                                            <td class="table-td">{{ $loan->amount }}</td>
                                                            <td class="table-td">{{ $loan->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $loans->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No loans found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Purchases -->
                <div class="col-md-6 mt-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Purchases</h4>
                        </div>
                        <div class="card-body px-6 pb-6">
                            @if ($purchases->count() > 0)
                                <div class="overflow-x-auto -mx-6">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden">
                                            <table
                                                class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                                <thead class="bg-slate-200 dark:bg-slate-700">
                                                    <tr>
                                                        <th scope="col" class="table-th">Date</th>
                                                        <th scope="col" class="table-th">Amount</th>
                                                        <th scope="col" class="table-th">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                                    @foreach ($purchases as $purchase)
                                                        <tr>
                                                            <td class="table-td">
                                                                {{ $purchase->created_at->format('d/m/Y') }}</td>
                                                            <td class="table-td">{{ $purchase->amount }}</td>
                                                            <td class="table-td">{{ $purchase->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-6">
                                                {{ $purchases->links('vendor.livewire.simple-bootstrap') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mt-5">
                                    No purchases found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <livewire:benefits.partials.apply-package-modal />
    <livewire:benefits.partials.apply-attendance-modal />
    <livewire:benefits.partials.apply-vacations-modal />

    <!-- Custom Base Benefit Modal -->
    <div>
        @if ($showAddCustomBaseBenefitModal)
            <x-modal wire:model="showAddCustomBaseBenefitModal">
                <x-slot name="title">Add Custom Base Benefit</x-slot>

                <div class="space-y-6">
                    @if ($errors->any())
                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div>
                                    <p class="font-medium">Please fix the following errors:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">

                        <x-select wire:model="baseBenefit.receiver" label="Paid to*"
                            errorMessage="{{ $errors->first('baseBenefit.receiver') }}">
                            <option value="" disabled selected>-- Select Receiver --</option>
                            @foreach ($benefitReceivers as $receiver)
                                <option value="{{ $receiver }}">{{ $receiver }}</option>
                            @endforeach
                        </x-select>


                        <x-text-input label="Name*" type="text" wire:model="baseBenefit.name"
                            errorMessage="{{ $errors->first('baseBenefit.name') }}" />

                        <x-text-input label="Amount*" type="number" step="0.01" wire:model="baseBenefit.amount"
                            errorMessage="{{ $errors->first('baseBenefit.amount') }}" />


                        <x-select wire:model="baseBenefit.type" label="Increment Type*"
                            errorMessage="{{ $errors->first('baseBenefit.type') }}">
                            <option value="" disabled selected>-- Select Type --</option>
                            @foreach ($benefitIncrementTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </x-select>




                        <x-text-input label="Start Date*" type="date" wire:model="baseBenefit.start_date"
                            errorMessage="{{ $errors->first('baseBenefit.start_date') }}" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-end gap-3">
                        <x-secondary-button wire:click="closeAddCustomBaseBenefitModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveCustomBaseBenefit"
                            loadingFunction="saveCustomBaseBenefit">
                            Save
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Custom Vacation Benefit Modal -->
    <div>
        @if ($showAddCustomVacationBenefitModal)
            <x-modal wire:model="showAddCustomVacationBenefitModal">
                <x-slot name="title">Add Custom Vacation Benefit</x-slot>

                <div class="space-y-6">
                    @if ($errors->any())
                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div>
                                    <p class="font-medium">Please fix the following errors:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        <x-text-input label="Name*" type="text" wire:model="vacationBenefit.name"
                            errorMessage="{{ $errors->first('vacationBenefit.name') }}" />

                        <x-select wire:model="vacationBenefit.type" label="Increment Type*"
                            errorMessage="{{ $errors->first('vacationBenefit.type') }}">
                            <option value="" disabled selected>-- Select Type --</option>
                            @foreach ($vacationBenefitTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach

                        </x-select>

                        <x-text-input label="Increment Rate*" type="number" step="0.01"
                            wire:model="vacationBenefit.inc_rate"
                            errorMessage="{{ $errors->first('vacationBenefit.inc_rate') }}" />

                        <x-text-input label="Hour Price*" type="number" step="0.01"
                            wire:model="vacationBenefit.hour_price"
                            errorMessage="{{ $errors->first('vacationBenefit.hour_price') }}" />

                        <x-text-input label="Current Balance*" type="number" step="0.01"
                            wire:model="vacationBenefit.current_balance"
                            errorMessage="{{ $errors->first('vacationBenefit.current_balance') }}" />

                        <x-text-input label="Max Balance*" type="number" step="0.01"
                            wire:model="vacationBenefit.max_balance"
                            errorMessage="{{ $errors->first('vacationBenefit.max_balance') }}" />

                        <x-text-input label="Apply Deadline (days after leave)" type="number"
                            wire:model="vacationBenefit.apply_deadline"
                            errorMessage="{{ $errors->first('vacationBenefit.apply_deadline') }}" />

                        <x-text-input label="Start Date*" type="date" wire:model="vacationBenefit.start_date"
                            errorMessage="{{ $errors->first('vacationBenefit.start_date') }}" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-end gap-3">
                        <x-secondary-button wire:click="closeAddCustomVacationBenefitModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveCustomVacationBenefit"
                            loadingFunction="saveCustomVacationBenefit">
                            Save
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Loan Modal -->
    <div>
        @if ($showAddLoanModal)
            <x-modal wire:model="showAddLoanModal">
                <x-slot name="title">Add Loan</x-slot>

                <div class="space-y-6">
                    @if ($errors->any())
                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div>
                                    <p class="font-medium">Please fix the following errors:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        <x-text-input label="Total Amount*" type="number" step="0.01" wire:model="loan.amount"
                            wire:change="updateRemainingAmount" errorMessage="{{ $errors->first('loan.amount') }}" />

                        <x-textarea label="Description" wire:model="loan.desc"
                            errorMessage="{{ $errors->first('loan.desc') }}" />

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-medium">Payment Plan</h3>
                                <div>
                                    <span class="text-sm mr-3">Remaining: {{ $loanRemainingAmount }}</span>
                                    <x-secondary-button wire:click="addLoanPayment" size="sm">
                                        <iconify-icon icon="mdi:plus"></iconify-icon> Add Payment
                                    </x-secondary-button>
                                </div>
                            </div>

                            @foreach ($loanPayments as $index => $payment)
                                <div class="border rounded-lg p-3 mb-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium">Payment {{ $index + 1 }}</h4>
                                        <button type="button" class="text-red-500"
                                            wire:click="removeLoanPayment({{ $index }})">
                                            <iconify-icon icon="mdi:delete"></iconify-icon>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <x-text-input label="Amount*" type="number" step="0.01"
                                            wire:model="loanPayments.{{ $index }}.amount"
                                            wire:change="updateRemainingAmount"
                                            errorMessage="{{ $errors->first('loanPayments.' . $index . '.amount') }}" />

                                        <x-text-input label="Due Date*" type="date"
                                            wire:model="loanPayments.{{ $index }}.due_date"
                                            errorMessage="{{ $errors->first('loanPayments.' . $index . '.due_date') }}" />

                                        <div class="col-span-2">
                                            <x-textarea label="Description"
                                                wire:model="loanPayments.{{ $index }}.desc"
                                                errorMessage="{{ $errors->first('loanPayments.' . $index . '.desc') }}" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-end gap-3">
                        <x-secondary-button wire:click="closeAddLoanModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveLoan" loadingFunction="saveLoan" :disabled="$loanRemainingAmount != 0">
                            Save
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Purchase Modal -->
    <div>
        @if ($showAddPurchaseModal)
            <x-modal wire:model="showAddPurchaseModal">
                <x-slot name="title">Add Purchase</x-slot>

                <div class="space-y-6">
                    @if ($errors->any())
                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div>
                                    <p class="font-medium">Please fix the following errors:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        <x-text-input label="Total Amount*" type="number" step="0.01"
                            wire:model="purchase.amount" wire:change="updateRemainingPurchaseAmount"
                            errorMessage="{{ $errors->first('purchase.amount') }}" />

                        <x-textarea label="Description" wire:model="purchase.desc"
                            errorMessage="{{ $errors->first('purchase.desc') }}" />

                        <div class="border-t pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-medium">Payment Plan</h3>
                                <div>
                                    <span class="text-sm mr-3">Remaining: {{ $purchaseRemainingAmount }}</span>
                                    <x-secondary-button wire:click="addPurchasePayment" size="sm">
                                        <iconify-icon icon="mdi:plus"></iconify-icon> Add Payment
                                    </x-secondary-button>
                                </div>
                            </div>

                            @foreach ($purchasePayments as $index => $payment)
                                <div class="border rounded-lg p-3 mb-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium">Payment {{ $index + 1 }}</h4>
                                        <button type="button" class="text-red-500"
                                            wire:click="removePurchasePayment({{ $index }})">
                                            <iconify-icon icon="mdi:delete"></iconify-icon>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <x-text-input label="Amount*" type="number" step="0.01"
                                            wire:model="purchasePayments.{{ $index }}.amount"
                                            wire:change="updateRemainingPurchaseAmount"
                                            errorMessage="{{ $errors->first('purchasePayments.' . $index . '.amount') }}" />

                                        <x-text-input label="Due Date*" type="date"
                                            wire:model="purchasePayments.{{ $index }}.due_date"
                                            errorMessage="{{ $errors->first('purchasePayments.' . $index . '.due_date') }}" />

                                        <div class="col-span-2">
                                            <x-textarea label="Description"
                                                wire:model="purchasePayments.{{ $index }}.desc"
                                                errorMessage="{{ $errors->first('purchasePayments.' . $index . '.desc') }}" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-end gap-3">
                        <x-secondary-button wire:click="closeAddPurchaseModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click.prevent="savePurchase" loadingFunction="savePurchase"
                            :disabled="$purchaseRemainingAmount != 0">
                            Save
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Extra Payment Modal -->
    <div>
        @if ($showAddExtraPaymentModal)
            <x-modal wire:model="showAddExtraPaymentModal">
                <x-slot name="title">Add Extra Payment</x-slot>

                <div class="space-y-6">
                    @if ($errors->any())
                        <div class="alert alert-warning mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <div>
                                    <p class="font-medium">Please fix the following errors:</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">

                        <x-text-input label="Amount*"
                            class="@error('extraPayment.amount') !border-danger-500 @enderror" type="number"
                            step="0.01" wire:model="extraPayment.amount"
                            errorMessage="{{ $errors->first('extraPayment.amount') }}" />

                        <x-text-input label="Due Date*"
                            class="@error('extraPayment.due_date') !border-danger-500 @enderror" type="date"
                            wire:model="extraPayment.due_date"
                            errorMessage="{{ $errors->first('extraPayment.due_date') }}" />

                        <x-textarea label="Description"
                            class="@error('extraPayment.desc') !border-danger-500 @enderror"
                            wire:model="extraPayment.desc"
                            errorMessage="{{ $errors->first('extraPayment.desc') }}" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="mt-4 flex justify-end gap-3">
                        <x-secondary-button wire:click="closeAddExtraPaymentModal">Cancel</x-secondary-button>
                        <x-primary-button wire:click.prevent="saveExtraPayment" loadingFunction="saveExtraPayment">
                            Save
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-modal>
        @endif
    </div>

    <!-- Employee Details Modal -->
    @if ($showEmployeeDetailsModal && $selectedPayrollEmployee)
        <x-modal wire:model="showEmployeeDetailsModal" size="xl">
            <x-slot name="title">
                <div class="flex items-center">
                    <div class="flex-none">
                        <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white">
                            {{ substr($selectedPayrollEmployee->employee->name ?? 'N/A', 0, 2) }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-slate-100 dark:text-white">
                            {{ $selectedPayrollEmployee->employee->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $selectedPayrollEmployee->position }} - {{ $selectedPayrollEmployee->department }}
                        </p>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-5">
                <!-- Payroll Summary Section -->
                <div>
                    <h4 class="text-base font-medium mb-3 border-b pb-2">Payroll Summary</h4>
                    <div class="grid  md:grid-cols-6 sm:grid-cols-1 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Gross Salary</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->gross_salary, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Social Insurance
                                Salary
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->insurance_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Other Amount</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->other_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employee Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->employee_insurance, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Employer Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->employer_insurance, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Total Insurance
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->total_insurance, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty Days</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->penalties_days, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Penalty Amount</h5>
                            <div class="text-sm font-semibold text-danger-500">
                                -{{ number_format($selectedPayrollEmployee->penalties_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Net After Penalty
                            </h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->net_after_penalty, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Extra Payments</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->extra_payments, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Hours</h5>
                            <div class="text-sm font-semibold">
                                {{ number_format($selectedPayrollEmployee->overtime_hours, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Overtime Amount
                            </h5>
                            <div class="text-sm font-semibold text-success-500">
                                +{{ number_format($selectedPayrollEmployee->overtime_amount, 2) }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-md p-3">
                            <h5 class="text-xs font-medium text-slate-500 dark:text-slate-300 mb-1">Adjustment Amount
                            </h5>
                            <div
                                class="text-sm font-semibold @if ($selectedPayrollEmployee->adj_amount >= 0) text-success-500 @else text-danger-500 @endif">
                                @if ($selectedPayrollEmployee->adj_amount >= 0)
                                    +
                                @endif
                                {{ number_format($selectedPayrollEmployee->adj_amount, 2) }}
                            </div>
                            @if (!empty($selectedPayrollEmployee->adj_desc))
                                <div class="text-xs text-slate-500 mt-1">{{ $selectedPayrollEmployee->adj_desc }}
                                </div>
                            @endif
                        </div>
                        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-md p-3 md:col-span-6">
                            <h5 class="text-xs font-medium text-primary-500 dark:text-primary-400 mb-1">Net Amount</h5>
                            <div class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($selectedPayrollEmployee->net_after_deductions, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tabs for different sections -->
                <div x-data="{ activeTab: 'attendance' }">
                    <div class="border-b border-slate-200 dark:border-slate-700">
                        <ul class="flex flex-wrap -mb-px">
                            <li class="mr-2">
                                <button @click="activeTab = 'attendance'"
                                    :class="activeTab === 'attendance' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Attendance Records
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'benefits'"
                                    :class="activeTab === 'benefits' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Benefit Payments
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'overtime'"
                                    :class="activeTab === 'overtime' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Overtime
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'extras'"
                                    :class="activeTab === 'extras' ? 'border-primary-500 text-primary-500' :
                                        'border-transparent text-slate-500 hover:text-slate-600 hover:border-slate-300'"
                                    class="inline-block py-2 px-4 text-sm font-medium border-b-2">
                                    Extra Payments
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Attendance Records Tab -->
                    <div x-show="activeTab === 'attendance'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Day</th>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Check In</th>
                                        <th class="table-th">Check Out</th>
                                        <th class="table-th">Total Hours</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeAttendance as $attendance)
                                        <tr>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</td>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                            <td class="table-td">
                                                {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i A') : 'N/A' }}
                                            </td>
                                            <td class="table-td">
                                                {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i A') : 'N/A' }}
                                            </td>
                                            <td class="table-td">
                                                <span
                                                    class="
                                            @if ($attendance->hours < $attendance->employee->benefitConfiguration->daily_working_hours) text-danger-500 @endif
                                            @if ($attendance->hours > $attendance->employee->benefitConfiguration->daily_working_hours) text-success-500 @endif
                                            ">
                                                    {{ $attendance->hours ?? 'N/A' }}
                                                    <small>Hours</small>
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                @if ($attendance->status === 'present')
                                                    <span class="badge bg-success-500 text-white">Present</span>
                                                @elseif($attendance->status === 'absent')
                                                    <span class="badge bg-danger-500 text-white">Absent</span>
                                                @elseif($attendance->status === 'late')
                                                    <span class="badge bg-warning-500 text-white">Late</span>
                                                @else
                                                    <span
                                                        class="badge @if ($attendance->is_approved) bg-success-500 @else bg-slate-500 @endif text-white">
                                                        {{ ucfirst($attendance->is_approved ? 'Approved' : 'Pending') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="table-td text-center py-4">No attendance records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Benefit Payments Tab -->
                    <div x-show="activeTab === 'benefits'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Benefit</th>
                                        <th class="table-th">Type</th>
                                        <th class="table-th">Amount</th>
                                        <th class="table-th">Status</th>
                                        <th class="table-th">Created At</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeBenefitPayments as $benefit)
                                        <tr>
                                            <td class="table-td">{{ $benefit->baseBenefit->name ?? 'N/A' }}</td>
                                            <td class="table-td">{{ ucfirst($benefit->baseBenefit->type ?? 'N/A') }}
                                            </td>
                                            <td class="table-td">{{ number_format($benefit->amount, 2) }}</td>
                                            <td class="table-td">
                                                @if ($benefit->status === 'pending')
                                                    <span class="badge bg-warning-500 text-white">Pending</span>
                                                @elseif($benefit->status === 'approved')
                                                    <span class="badge bg-info-500 text-white">Approved</span>
                                                @elseif($benefit->status === 'paid')
                                                    <span class="badge bg-success-500 text-white">Paid</span>
                                                @elseif($benefit->status === 'rejected')
                                                    <span class="badge bg-danger-500 text-white">Rejected</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-500 text-white">{{ ucfirst($benefit->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="table-td">{{ $benefit->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No benefit payments
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Overtime Tab -->
                    <div x-show="activeTab === 'overtime'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Hours</th>
                                        <th class="table-th">Rate</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeOvertimes as $overtime)
                                        <tr>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</td>
                                            <td class="table-td">{{ $overtime->hours }}</td>
                                            <td class="table-td">{{ $overtime->rate ?? 'Standard' }}</td>
                                            <td class="table-td">
                                                <span
                                                    class="badge bg-success-500 text-white">{{ ucfirst($overtime->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No overtime records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Extra Payments Tab -->
                    <div x-show="activeTab === 'extras'" class="mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead>
                                    <tr>
                                        <th class="table-th">Description</th>
                                        <th class="table-th">Amount</th>
                                        <th class="table-th">Due Date</th>
                                        <th class="table-th">Status</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($employeeExtraPayments as $payment)
                                        <tr>
                                            <td class="table-td">{{ $payment->name }}</td>
                                            <td class="table-td">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="table-td">
                                                {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
                                            <td class="table-td">
                                                @if ($payment->status === 'pending')
                                                    <span class="badge bg-warning-500 text-white">Pending</span>
                                                @elseif($payment->status === 'approved')
                                                    <span class="badge bg-info-500 text-white">Approved</span>
                                                @elseif($payment->status === 'paid')
                                                    <span class="badge bg-success-500 text-white">Paid</span>
                                                @elseif($payment->status === 'rejected')
                                                    <span class="badge bg-danger-500 text-white">Rejected</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-500 text-white">{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center py-4">No extra payments
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-secondary-button wire:click="closeEmployeeDetailsModal">Close</x-secondary-button>
                </div>
            </x-slot>
        </x-modal>
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

                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="release_date" class="form-label">Release Date</label>
                                        <input type="date"
                                            class="form-control @error('release_date') !border-danger-500 @enderror"
                                            wire:model="release_date">
                                        @error('release_date')
                                            <span class="font-Inter text-sm text-danger-500">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-span-12 xl:col-span-6">
                                        <label for="absent_date" class="form-label">Absent Date</label>
                                        <input type="date"
                                            class="form-control @error('absent_date') !border-danger-500 @enderror"
                                            wire:model="absent_date">
                                        @error('absent_date')
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
</div>
