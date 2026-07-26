@extends('components.layouts.guest')

@section('content')
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Thank You!</h1>
                <p class="text-lg text-gray-600">Your application has been submitted successfully</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center py-12">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">What happens next?</h2>
                    <div class="space-y-3 text-left">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <p class="text-gray-600">Our HR team will review your application</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <p class="text-gray-600">We'll contact you shortly</p>
                        </div>
                    </div>
                </div>

                {{-- <div class="border-t pt-6">
                    <p class="text-sm text-gray-500 mb-4">
                        Have questions? Feel free to reach out to us.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="mailto:hr@company.com" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Contact HR
                        </a>
                        <a href="{{ url('/') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div> --}}
            </div>


        </div>
    </div>
@endsection