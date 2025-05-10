{{-- Just reuse the employee show view but only display the documents section --}}
<div class="space-y-5 profile-page mx-auto">
    <div class="flex justify-between">
        <div class="flex gap-5">
            <h4><b>My Documents</b></h4>
        </div>
    </div>

    @include('livewire.employee.employee-show')
</div> 