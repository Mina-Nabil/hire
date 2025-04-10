<div>
    <div class="card">
        <div class="card-body flex flex-col items-center text-center p-10">
            <div class="w-24 h-24 bg-success-500 rounded-full flex items-center justify-center mb-6">
                <iconify-icon class="text-5xl text-white" icon="ph:check-bold"></iconify-icon>
            </div>
            
            <h3 class="text-2xl font-bold text-slate-900 mb-3">Application Submitted Successfully!</h3>
            
            <p class="text-slate-600 mb-8 max-w-xl">
                Thank you for submitting your application. Our recruitment team will review your application and contact you soon.
                Please keep an eye on your email for updates regarding your application status.
            </p>
            
            <div class="space-y-3">
                <p class="text-sm text-slate-500">Application Reference: <span class="font-medium">{{ session('applicationReference', 'APP-' . date('YmdHis')) }}</span></p>
                <p class="text-sm text-slate-500">Submitted on: <span class="font-medium">{{ now()->format('F d, Y \a\t h:i A') }}</span></p>
            </div>
            
            <div class="flex space-x-3 mt-10">
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:house-bold"></iconify-icon>
                    Back to Home
                </a>
                <a href="{{ route('recruitment.vacancies') }}" class="btn btn-primary">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:magnifying-glass-bold"></iconify-icon>
                    View Other Vacancies
                </a>
            </div>
        </div>
    </div>
</div> 