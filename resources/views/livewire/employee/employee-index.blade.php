<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                {{ __('Employees Management') }}
            </h4>
        </div>
        
        @can('create', App\Models\Personel\Employee::class)
        <div class="md:mb-6 mb-4">
            <a href="{{ route('employees.create') }}" class="btn inline-flex justify-center btn-primary">
                <iconify-icon icon="heroicons-outline:plus"></iconify-icon>
                <span class="ml-2">{{ __('Create Employee') }}</span>
            </a>
        </div>
        @endcan
    </div>
    <div class="card">
        <header class="card-header cust-card-header noborder">
            <iconify-icon wire:loading wire:target="search" class="loading-icon text-lg"
                icon="line-md:loading-twotone-loop"></iconify-icon>
            <input type="text" class="form-control !pl-9 mr-1 basis-1/4" placeholder="{{ __('Search employees') }}"
                wire:model.live.debounce.500ms="search">
        </header>

        <div class="card-body px-6 pb-6">
            <div class=" -mx-6">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <thead
                                class=" border-t border-slate-100 dark:border-slate-800 bg-slate-200 dark:bg-slate-700">
                                <tr>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Email') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Phone') }}
                                    </th>
                                    <th scope="col" class=" table-th ">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td class="table-td">
                                            {{ $employee->name }}
                                        </td>
                                        <td class="table-td">
                                            {{ $employee->email }}
                                        </td>
                                        <td class="table-td">
                                            {{ $employee->phone }}
                                        </td>
                                        <td class="table-td">
                                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm inline-flex justify-center btn-dark">
                                                <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                <span class="ml-2">View</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="table-td text-center">
                                            {{ __('No employees found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $employees->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>
</div> 