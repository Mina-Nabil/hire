<div>
    <div class="flex justify-between flex-wrap items-center mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block">
            Applied Benefits Report
        </h4>
    </div>

    {{-- Card 1: Filters --}}
    <div class="card w-full mb-6">
        <header class="card-header cust-card-header noborder">
            <h5 class="card-title">Filters</h5>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label" for="fromDate">From</label>
                    <input type="date"
                        id="fromDate"
                        class="form-control"
                        wire:model.live="fromDate" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="toDate">To</label>
                    <input type="date"
                        id="toDate"
                        class="form-control"
                        wire:model.live="toDate" />
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Table --}}
    <div class="card w-full">
        <header class="card-header cust-card-header noborder">
            <h5 class="card-title">Applied Benefits per Active Employee</h5>
            <iconify-icon wire:loading class="loading-icon text-lg" icon="line-md:loading-twotone-loop"></iconify-icon>
        </header>
        <div class="card-body px-6 pb-6">
            <div class="overflow-x-auto -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead class="bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class="table-th">Employee Name</th>
                                    <th scope="col" class="table-th">Vacation Name</th>
                                    <th scope="col" class="table-th">Total Days</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse($reportData as $row)
                                    <tr>
                                        <td class="table-td">{{ $row['employee_name'] }}</td>
                                        <td class="table-td">{{ $row['vacation_name'] }}</td>
                                        <td class="table-td">{{ number_format($row['total_days'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="table-td text-center">
                                            No applied benefits found for the selected date range
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
