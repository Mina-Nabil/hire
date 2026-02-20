<div>
    <div class="mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900">
            Add a mission to selected employees
        </h4>
    </div>

    @if (!$showResults)
        <div class="card w-full">
            <div class="card-body px-6 pb-6">
                <form wire:submit="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Section 1: Left - Employee list with checkboxes --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="font-semibold text-slate-700 dark:text-slate-300">Select Employees</h5>
                                <label class="inline-flex items-center cursor-pointer text-sm">
                                    <input type="checkbox"
                                        wire:click="selectAll"
                                        @if(count($selectedEmployeeIds) === $this->employees->count() && $this->employees->count() > 0) checked @endif
                                        class="form-checkbox rounded border-slate-300">
                                    <span class="ml-2">Select All</span>
                                </label>
                            </div>
                            <div class="border border-slate-200 dark:border-slate-600 rounded-lg max-h-[400px] overflow-y-auto">
                                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @forelse($this->employees as $employee)
                                        <label class="flex items-center p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                                            <input type="checkbox"
                                                wire:model="selectedEmployeeIds"
                                                value="{{ $employee->id }}"
                                                class="form-checkbox rounded border-slate-300">
                                            <span class="ml-3 text-slate-700 dark:text-slate-300">{{ $employee->name }}</span>
                                        </label>
                                    @empty
                                        <div class="p-6 text-center text-slate-500">
                                            No current employees found
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Right - Form fields --}}
                        <div class="space-y-4">
                            <h5 class="font-semibold text-slate-700 dark:text-slate-300">Mission Details</h5>
                            <div class="space-y-4">
                                <div class="form-group">
                                    <label class="form-label" for="startDate">Start Date</label>
                                    <input type="date"
                                        id="startDate"
                                        class="form-control"
                                        wire:model="startDate" />
                                    @error('startDate')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="endDate">End Date</label>
                                    <input type="date"
                                        id="endDate"
                                        class="form-control"
                                        wire:model="endDate" />
                                    @error('endDate')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="hours">Number of Hours</label>
                                    <input type="number"
                                        id="hours"
                                        class="form-control"
                                        wire:model="hours"
                                        step="0.25"
                                        min="0.25"
                                        placeholder="e.g. 8 or 40" />
                                    @error('hours')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="pt-4">
                                <button type="submit"
                                    class="btn btn-dark"
                                    wire:loading.attr="disabled">
                                    <iconify-icon wire:loading wire:target="submit"
                                        class="loading-icon inline-block mr-2"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                    Add Missions
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Results Tab --}}
        <div class="card w-full">
            <header class="card-header cust-card-header noborder">
                <h5 class="card-title">Summary of Added Missions</h5>
                <button type="button"
                    class="btn btn-sm btn-outline-dark"
                    wire:click="resetForm">
                    Add More Missions
                </button>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="font-semibold text-success-600 dark:text-success-400 mb-3">
                            Successfully Added ({{ count($results['success']) }})
                        </h6>
                        @if (count($results['success']) > 0)
                            <ul class="list-disc list-inside space-y-1 text-slate-700 dark:text-slate-300">
                                @foreach ($results['success'] as $item)
                                    <li>{{ $item['name'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-slate-500">None</p>
                        @endif
                    </div>
                    <div>
                        <h6 class="font-semibold text-danger-600 dark:text-danger-400 mb-3">
                            Failed ({{ count($results['failed']) }})
                        </h6>
                        @if (count($results['failed']) > 0)
                            <ul class="space-y-2 text-slate-700 dark:text-slate-300">
                                @foreach ($results['failed'] as $item)
                                    <li>
                                        <span class="font-medium">{{ $item['name'] }}</span>
                                        <span class="text-slate-500 text-sm">— {{ $item['reason'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-slate-500">None</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
