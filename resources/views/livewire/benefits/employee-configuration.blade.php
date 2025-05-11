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
                        Edit Main Package
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
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="setActiveTab"
                            icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="tabs-tabContent">
        <!-- Employee Info & Benefits Tab -->
        <div class="tab-pane fade @if ($activeTab === 'info') show active @endif" id="tabs-info" role="tabpanel"
            aria-labelledby="tabs-info-tab">
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
                                            {{ $employee->benefitConfiguration->benefitPackage->name ?? '-' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-5 font-weight-bold">Attendance Calculation:</div>
                                        <div class="col-md-7">
                                            {{ $this->getAttendanceCalculationLabel($employee->benefitConfiguration->attendace_calculation) }}
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
                                                                <td class="table-td"> @if($payment->payable_id) {{ $payment->payable_type }} @else - @endif</td>
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
</div>
