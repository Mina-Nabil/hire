<div>
    <div class="mb-6">
        <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 dark:text-slate-100">
            Add a mission to selected employees
        </h4>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Select employees and specify the mission period and
            hours</p>
    </div>

    @if (!$showResults)
        <div class="card w-full shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="card-body px-6 py-6">
                <form wire:submit="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Section 1: Left - Employee list with checkboxes --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-100 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="font-semibold text-slate-800 dark:text-slate-200 text-base">Select Employees
                                </h5>
                                <label
                                    class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                                    <input type="checkbox" wire:click="selectAll"
                                        @if (count($selectedEmployeeIds) === $this->employees->count() && $this->employees->count() > 0) checked @endif
                                        class="form-checkbox rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <span>Select All</span>
                                </label>
                            </div>
                            <div
                                class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 shadow-inner max-h-[400px] overflow-y-auto">
                                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @forelse($this->employees as $employee)
                                        <label
                                            class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors first:rounded-t-lg last:rounded-b-lg">
                                            <input type="checkbox" wire:model="selectedEmployeeIds"
                                                value="{{ $employee->id }}"
                                                class="form-checkbox rounded border-slate-300 text-slate-900 focus:ring-slate-500 shrink-0">
                                            <span
                                                class="text-slate-700 dark:text-slate-300 text-sm">{{ $employee->name }}</span>
                                        </label>
                                    @empty
                                        <div class="p-8 text-center text-slate-500 dark:text-slate-400">
                                            No current employees found
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @if ($this->employees->count() > 0)
                                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                    {{ count($selectedEmployeeIds) }} of {{ $this->employees->count() }} selected
                                </p>
                            @endif
                        </div>

                        {{-- Section 2: Right - Form fields --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-5 border border-slate-100 dark:border-slate-700">
                            <h5 class="font-semibold text-slate-800 dark:text-slate-200 text-base mb-4">Mission Details
                            </h5>
                            <div class="space-y-5">
                                <div class="form-group">
                                    <label
                                        class="form-label block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
                                        for="startDate">Start Date</label>
                                    <input type="date" id="startDate"
                                        class="form-control w-full rounded-lg border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-shadow"
                                        wire:model="startDate" />
                                    @error('startDate')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label
                                        class="form-label block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
                                        for="endDate">End Date</label>
                                    <input type="date" id="endDate"
                                        class="form-control w-full rounded-lg border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-shadow"
                                        wire:model="endDate" />
                                    @error('endDate')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label
                                        class="form-label block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
                                        for="hours">Number of Hours</label>
                                    <input type="number" id="hours"
                                        class="form-control w-full rounded-lg border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-shadow"
                                        wire:model="hours" step="0.25" min="0.25" placeholder="e.g. 8 or 40" />
                                    @error('hours')
                                        <p class="mt-1 text-sm text-danger-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-600">
                                <button type="submit"
                                    class="btn btn-dark inline-flex items-center gap-2 px-6 py-2.5 rounded-lg font-medium shadow-sm hover:shadow transition-all"
                                    wire:loading.attr="disabled">
                                    <iconify-icon wire:loading wire:target="submit" class="loading-icon text-lg"
                                        icon="line-md:loading-twotone-loop"></iconify-icon>
                                    <iconify-icon wire:loading.remove wire:target="submit" icon="heroicons:plus-circle"
                                        class="text-lg"></iconify-icon>
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
        <div class="card w-full shadow-sm border border-slate-200 dark:border-slate-700">
            <header class="card-header cust-card-header noborder flex flex-wrap items-center justify-between gap-4">
                <h5 class="card-title">Summary of Added Missions</h5>
                <button type="button" class="btn btn-sm btn-outline-dark inline-flex items-center gap-2"
                    wire:click="resetForm">
                    <iconify-icon icon="heroicons:plus"></iconify-icon>
                    Add More Missions
                </button>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-green-50 dark:bg-green-900/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                        <h6 class="font-semibold text-green-700 dark:text-green-400 mb-3 flex items-center gap-2">
                            <iconify-icon icon="heroicons:check-circle" class="text-lg"></iconify-icon>
                            Successfully Added ({{ count($results['success']) }})
                        </h6>
                        @if (count($results['success']) > 0)
                            <ul class="list-disc list-inside space-y-1.5 text-slate-700 dark:text-slate-300 text-sm">
                                @foreach ($results['success'] as $item)
                                    <li>{{ $item['name'] }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-slate-500 dark:text-slate-400 text-sm">None</p>
                        @endif
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-5 border border-red-200 dark:border-red-800">
                        <h6 class="font-semibold text-red-700 dark:text-red-400 mb-3 flex items-center gap-2">
                            <iconify-icon icon="heroicons:exclamation-circle" class="text-lg"></iconify-icon>
                            Failed ({{ count($results['failed']) }})
                        </h6>
                        @if (count($results['failed']) > 0)
                            <ul class="space-y-2 text-sm">
                                @foreach ($results['failed'] as $item)
                                    <li class="text-slate-700 dark:text-slate-300">
                                        <span class="font-medium">{{ $item['name'] }}</span>
                                        <span class="text-slate-500 dark:text-slate-400">— {{ $item['reason'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-slate-500 dark:text-slate-400 text-sm">None</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
