<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Employee Benefits Configuration</h3>
        </div>
        <div class="card-body">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'info' ? 'active' : '' }}" 
                        wire:click="setActiveTab('info')" role="tab">
                        Employee & Benefits
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'payments' ? 'active' : '' }}"
                        wire:click="setActiveTab('payments')" role="tab">
                        Payments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'vacations' ? 'active' : '' }}"
                        wire:click="setActiveTab('vacations')" role="tab">
                        Vacations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'loans' ? 'active' : '' }}"
                        wire:click="setActiveTab('loans')" role="tab">
                        Loans & Purchases
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-4">
                <!-- Employee Info & Benefits Tab -->
                <div class="tab-pane fade {{ $activeTab == 'info' ? 'show active' : '' }}" role="tabpanel">
                    <div class="row">
                        <!-- Employee Info Card -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Employee Information</h4>
                                </div>
                                <div class="card-body">
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
                                        <div class="col-md-8">{{ $employee->birth_date ? $employee->birth_date->format('d/m/Y') : '-' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 font-weight-bold">Employment Date:</div>
                                        <div class="col-md-8">{{ $employee->employment_date ? $employee->employment_date->format('d/m/Y') : '-' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4 font-weight-bold">Address:</div>
                                        <div class="col-md-8">{{ $employee->address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Benefit Configuration Card -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Benefit Configuration</h4>
                                </div>
                                <div class="card-body">
                                    @if($employee->benefitConfiguration)
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Package:</div>
                                            <div class="col-md-7">{{ $employee->benefitConfiguration->benefitPackage->name ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Attendance Calculation:</div>
                                            <div class="col-md-7">{{ $this->getAttendanceCalculationLabel($employee->benefitConfiguration->attendace_calculation) }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Working Day Start:</div>
                                            <div class="col-md-7">{{ $employee->benefitConfiguration->working_day_start_min }} - {{ $employee->benefitConfiguration->working_day_start_max }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Working Day End:</div>
                                            <div class="col-md-7">{{ $employee->benefitConfiguration->working_day_end_min }} - {{ $employee->benefitConfiguration->working_day_end_max }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Daily Working Hours:</div>
                                            <div class="col-md-7">{{ $employee->benefitConfiguration->daily_working_hours }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5 font-weight-bold">Overtime Rate:</div>
                                            <div class="col-md-7">{{ $employee->benefitConfiguration->overtime_rate }}</div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            No benefit configuration found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Benefits Section -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Base Benefits</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($employeeBenefits) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Receiver</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employeeBenefits as $benefit)
                                                        <tr>
                                                            <td>{{ $benefit->name }}</td>
                                                            <td>{{ $this->getBenefitTypeLabel($benefit->type) }}</td>
                                                            <td>{{ $benefit->amount }}</td>
                                                            <td>{{ $this->getReceiverLabel($benefit->receiver) }}</td>
                                                            <td>
                                                                @if($benefit->end_date)
                                                                    <span class="badge bg-danger">Ended: {{ $benefit->end_date->format('d/m/Y') }}</span>
                                                                @else
                                                                    <span class="badge bg-success">Active</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No base benefits found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Vacation Benefits</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($employeeVacations) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Inc. Rate</th>
                                                        <th>Hour Price</th>
                                                        <th>Current/Max</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employeeVacations as $vacation)
                                                        <tr>
                                                            <td>{{ $vacation->name }}</td>
                                                            <td>{{ $this->getBenefitTypeLabel($vacation->type) }}</td>
                                                            <td>{{ $vacation->inc_rate }}</td>
                                                            <td>{{ $vacation->hour_price }}</td>
                                                            <td>{{ $vacation->current_balance }}/{{ $vacation->max_balance }}</td>
                                                            <td>
                                                                @if($vacation->end_date)
                                                                    <span class="badge bg-danger">Ended: {{ $vacation->end_date->format('d/m/Y') }}</span>
                                                                @else
                                                                    <span class="badge bg-success">Active</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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

                <!-- Payments Tab -->
                <div class="tab-pane fade {{ $activeTab == 'payments' ? 'show active' : '' }}" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Employee Payments</h4>
                        </div>
                        <div class="card-body">
                            @if(count($employeePayments) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Benefit</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employeePayments as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ $payment->baseBenefit->name ?? '-' }}</td>
                                                    <td>{{ $payment->amount }}</td>
                                                    <td>
                                                        @if($payment->status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($payment->status == 'approved')
                                                            <span class="badge bg-info">Approved</span>
                                                        @elseif($payment->status == 'paid')
                                                            <span class="badge bg-success">Paid</span>
                                                        @elseif($payment->status == 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->desc ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No payments found for this employee.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Vacations Tab -->
                <div class="tab-pane fade {{ $activeTab == 'vacations' ? 'show active' : '' }}" role="tabpanel">
                    <div class="row">
                        <!-- Applied Vacations -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Applied Vacations</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($appliedVacations) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Hours</th>
                                                        <th>Status</th>
                                                        <th>New Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($appliedVacations as $vacation)
                                                        <tr>
                                                            <td>{{ $vacation->created_at->format('d/m/Y') }}</td>
                                                            <td>{{ $vacation->vacationBenefit->name ?? '-' }}</td>
                                                            <td>{{ $vacation->hours }}</td>
                                                            <td>
                                                                @if($vacation->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($vacation->status == 'approved')
                                                                    <span class="badge bg-success">Approved</span>
                                                                @elseif($vacation->status == 'rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $vacation->new_balance }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No applied vacations found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Gained Vacations -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Gained Vacations</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($gainedVacations) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Days</th>
                                                        <th>New Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($gainedVacations as $vacation)
                                                        <tr>
                                                            <td>{{ $vacation->created_at->format('d/m/Y') }}</td>
                                                            <td>{{ $vacation->vacationBenefit->name ?? '-' }}</td>
                                                            <td>{{ $vacation->days }}</td>
                                                            <td>{{ $vacation->new_balance }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No gained vacations found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loans & Purchases Tab -->
                <div class="tab-pane fade {{ $activeTab == 'loans' ? 'show active' : '' }}" role="tabpanel">
                    <div class="row">
                        <!-- Loans -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Loans</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($loans) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($loans as $loan)
                                                        <tr>
                                                            <td>{{ $loan->created_at->format('d/m/Y') }}</td>
                                                            <td>{{ $loan->amount }}</td>
                                                            <td>{{ $loan->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No loans found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Purchases -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Purchases</h4>
                                </div>
                                <div class="card-body">
                                    @if(count($purchases) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchases as $purchase)
                                                        <tr>
                                                            <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
                                                            <td>{{ $purchase->amount }}</td>
                                                            <td>{{ $purchase->desc ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No purchases found for this employee.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
