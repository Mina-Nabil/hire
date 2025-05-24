<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Import Data using Excel Sheet
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <button wire:click="openFileUpload"
                class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold"></iconify-icon>
                Upload Sheet
            </button>
            <button wire:click="downloadTemplate"
                class="btn inline-flex justify-center btn-light dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:download-bold"></iconify-icon>
                Download Template
            </button>
        </div>
    </div>
    @if ($showUploadModal)
        <x-modal wire:model="showUploadModal">
            <x-slot name="title">Upload Data</x-slot>
            <div class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <button wire:click="downloadTemplate" class="btn btn-secondary">
                        <i class="fas fa-download mr-2"></i> Download Template
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Excel File</label>
                        <input type="file" wire:model="file" class="mt-1 block w-full" accept=".xlsx,.xls">
                        @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-secondary-button wire:click="closeUploadModal">Cancel</x-secondary-button>
                    <x-primary-button wire:click="uploadSheet" loadingFunction="uploadSheet">Upload</x-primary-button>
                </div>
            </x-slot>
        </x-modal>
    @endif

    @if (!empty($migrationResults))
        <div class="mt-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach ($migrationResults as $key => $results)
                        <button wire:click="setActiveTab('{{ $key }}')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === $key ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ ucfirst($key) }}
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2 rounded-full text-xs">
                                {{ count($results) }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="mt-4">
                @foreach ($migrationResults as $key => $results)
                    <div x-show="$wire.activeTab === '{{ $key }}'" class="space-y-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if (!empty($results))
                                            @foreach (array_keys($results[0]) as $header)
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ ucfirst(str_replace('_', ' ', $header)) }}
                                                </th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($results as $row)
                                        <tr class="{{ isset($row['is_warning']) && $row['is_warning'] ? 'bg-yellow-50' : '' }}">
                                            @foreach ($row as $key => $value)
                                                @if ($key !== 'is_warning')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
