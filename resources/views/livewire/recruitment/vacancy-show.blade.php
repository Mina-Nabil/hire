<div class="space-y-5 profile-page mx-auto">

    <div class="flex justify-between">
        <div class="flex gap-5">
            <h4>
                <b>{{ $vacancy->position->name }} Vacancy</b>
            </h4>
            <div class="dropdown relative">
                <button class="btn inline-flex justify-center btn-dark items-center btn-sm" type="button"
                    id="darkDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                    <iconify-icon class="text-xl ltr:ml-2 rtl:mr-2" icon="ic:round-keyboard-arrow-down"></iconify-icon>
                </button>
                <ul
                    class=" dropdown-menu min-w-max absolute text-sm text-slate-700 dark:text-white hidden bg-white dark:bg-slate-700 shadow
                                z-[2] float-left overflow-hidden list-none text-left rounded-lg mt-1 m-0 bg-clip-padding border-none">

                    <li wire:click='$dispatch("showConfirmation", {
                        title: "Delete Applicant",
                        message: "Are you sure you want to delete this applicant?",
                        color: "danger",
                        callback: "deleteApplicant",
                    })'
                        class="text-slate-600 dark:text-white block font-Inter font-normal px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-600
                                        dark:hover:text-white cursor-pointer">
                        Delete Vacancy
                    </li>

                </ul>
            </div>
        </div>

    </div>



    <div class="card-body flex flex-col col-span-2" >
        <div class="card-text h-full">

            <div class="flex" wire:ignore>
                <ul class="nav nav-tabs flex flex-col md:flex-row flex-wrap list-none border-b-0 pl-0" id="tabs-tab"
                    role="tablist">
                    <li class="nav-item" role="presentation" wire:click="changeSection('info')">
                        <a href="#tabs-profile-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'info') active @endif dark:text-slate-300"
                            id="tabs-profile-withIcon-tab" data-bs-toggle="pill" data-bs-target="#tabs-profile-withIcon"
                            role="tab" aria-controls="tabs-profile-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="hugeicons:new-job"></iconify-icon>
                            Vacancy Information</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="changeSection('applications')">
                        <a href="#tabs-messages-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'applications') active @endif dark:text-slate-300"
                            id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                            data-bs-target="#tabs-messages-withIcon" role="tab"
                            aria-controls="tabs-messages-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="healthicons:city-worker"></iconify-icon>
                            Applicants & Interviews </a>
                    </li>
                    <li class="nav-item" role="presentation" wire:click="changeSection('manage')">
                        <a href="#tabs-messages-withIcon"
                            class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent  @if ($section === 'manage') active @endif dark:text-slate-300"
                            id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                            data-bs-target="#tabs-messages-withIcon" role="tab"
                            aria-controls="tabs-messages-withIcon" aria-selected="false">
                            <iconify-icon class="mr-1" icon="mdi:settings"></iconify-icon>
                            Manage Vacancy </a>
                    </li>

                </ul>
                <div>
                    <h4>
                        <iconify-icon class="ml-3" style="position: absolute" wire:loading wire:target="changeSection"
                            icon="svg-spinners:180-ring"></iconify-icon>
                    </h4>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="tabs-tabContent">

                @if ($section === 'info')
                    <div class="tab-pane fade show active" id="tabs-profile-withIcon" role="tabpanel"
                        aria-labelledby="tabs-profile-withIcon-tab">
                        @include('livewire.recruitment.partials.vacancy-information')
                    </div>
                @endif

                @if ($section === 'applications')
                    <div class="tab-pane fade show active" id="tabs-messages-withIcon" role="tabpanel"
                        aria-labelledby="tabs-messages-withIcon-tab">
                        @include('livewire.recruitment.partials.vacancy-applicants')
                    </div>
                @endif

                @if ($section === 'manage')
                    <div class="tab-pane fade show active" id="tabs-messages-withIcon" role="tabpanel"
                        aria-labelledby="tabs-messages-withIcon-tab">
                        @include('livewire.recruitment.partials.vacancy-manage')
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Include all the interview management modals -->
    @include('livewire.recruitment.partials.interview-modals')
</div>
