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
            <x-slot name="title">Upload Import Sheet</x-slot>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <div class="from-group">
                    <div class="input-area">
                        <label for="file" class="form-label">Select File</label>
                        <input type="file" wire:model="file"
                            class="form-control @error('file') !border-danger-500 @enderror" accept=".xlsx,.xls">
                        <p class="text-sm text-slate-500 mt-1">Supported formats: XLSX, XLS (Max size: 20MB)</p>
                    </div>
                    @error('file')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- Modal footer -->
            <x-slot name="footer">
                <x-secondary-button wire:click="closeUploadModal">Close</x-secondary-button>
                <x-primary-button wire:click.prevent="uploadSheet"
                    loadingFunction="uploadSheet">Upload</x-primary-button>
            </x-slot>
        </x-modal>
    @endif

</div>
