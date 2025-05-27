<div>
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse flex-col items-start">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Import Data using Excel Sheet
            </h4>
            <p class="text-sm text-gray-500">
                Only valid data will be imported, invalid data will be highlighted in yellow.
            </p>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <x-primary-button wire:click="openFileUpload" loadingFunction="openFileUpload" class="m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold"></iconify-icon>
                Upload Sheet
            </x-primary-button>
            <x-primary-button wire:click="downloadTemplate" class="m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:download-bold"></iconify-icon>
                Download Template
            </x-primary-button>
            @if (!empty($migrationResults))
                <x-primary-button wire:click="importData" loadingFunction="importData" class="m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:check-bold"></iconify-icon>
                    Save Data
                </x-primary-button>
            @endif
        </div>

    </div>

    @if (!empty($migrationResults))
        <div class="card mt-8">
            <div class="card-header border-b border-gray-200">
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

            <div class="card-body">
                @foreach ($migrationResults as $key => $results)
                    <div x-show="$wire.activeTab === '{{ $key }}'" class="space-y-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @if (!empty($results))
                                            @foreach (array_keys($results[0]) as $header)
                                                @if ($header !== 'not_valid')
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        {{ ucfirst(str_replace('_', ' ', $header)) }}
                                                    </th>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($results as $row)
                                        <tr
                                            class="{{ isset($row['not_valid']) && $row['not_valid'] ? 'bg-warning-500' : '' }}">
                                            @foreach ($row as $key => $value)
                                                @if ($key !== 'not_valid')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        @if (is_array($value))
                                                            <ul>
                                                                @foreach ($value as $item)
                                                                    @if (!$item['not_valid'])
                                                                        <li>
                                                                            <strong>{{ $item['name'] }} </strong> :
                                                                            <strong>Min: </strong>
                                                                            {{ $item['min'] }} -
                                                                            <strong>Max: </strong>
                                                                            {{ $item['max'] }}
                                                                            <strong>Paid to: </strong>
                                                                            {{ $item['to'] }} -
                                                                            <strong>Type: </strong>
                                                                            {{ $item['type'] }}
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            {{ $value }}
                                                        @endif
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



    @if ($showUploadModal)
        <x-modal wire:model="showUploadModal">
            <x-slot name="title">Upload Data</x-slot>
            <div class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                </div>
                <button wire:click="downloadTemplate" class="btn btn-secondary">
                    <i class="fas fa-download mr-2"></i> Download Template
                </button>


                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Excel File</label>
                        <input type="file" wire:model="file" class="mt-1 block w-full" accept=".xlsx,.xls">
                        @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
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
</div>
