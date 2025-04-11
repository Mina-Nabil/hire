<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-md">
        <h4 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Basic Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Position</h5>
                <p class="font-medium">{{ $vacancy->position->name }}</p>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Department</h5>
                <p class="font-medium">{{ $vacancy->position->department->name }}</p>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Type</h5>
                <p class="font-medium">{{ $vacancyTypes[$vacancy->type] ?? $vacancy->type }}</p>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Status</h5>
                <p>
                    <span class="badge {{ $vacancy->status === 'open' ? 'bg-success-200' : 'bg-danger-200' }}">
                        {{ $vacancyStatuses[$vacancy->status] ?? ucfirst($vacancy->status) }}
                    </span>
                </p>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Created Date</h5>
                <p>{{ $vacancy->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Closing Date</h5>
                <p>{{ $vacancy->closing_date ? $vacancy->closing_date->format('d M Y') : 'Not set' }}</p>
            </div>
        </div>
    </div>

    <!-- Assigned Personnel -->
    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-md">
        <h4 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Assigned Personnel</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Assigned To</h5>
                <div class="flex items-center">
                    <div class="h-9 w-9 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm mr-2">
                        {{ substr($vacancy->assigned_to_user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $vacancy->assigned_to_user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $vacancy->assigned_to_user->email }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">Hiring Manager</h5>
                <div class="flex items-center">
                    <div class="h-9 w-9 rounded-full bg-success-500 flex items-center justify-center text-white text-sm mr-2">
                        {{ substr($vacancy->hiring_manager->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $vacancy->hiring_manager->name }}</p>
                        <p class="text-xs text-slate-500">{{ $vacancy->hiring_manager->email }}</p>
                    </div>
                </div>
            </div>
            <div>
                <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">HR Manager</h5>
                <div class="flex items-center">
                    <div class="h-9 w-9 rounded-full bg-info-500 flex items-center justify-center text-white text-sm mr-2">
                        {{ substr($vacancy->hr_manager->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium">{{ $vacancy->hr_manager->name }}</p>
                        <p class="text-xs text-slate-500">{{ $vacancy->hr_manager->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details -->
    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-md">
        <h4 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Job Details</h4>
        
        @if($vacancy->job_responsibilities)
        <div class="mb-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">Responsibilities</h5>
            <div class="prose max-w-none dark:prose-invert">
                {!! nl2br(e($vacancy->job_responsibilities)) !!}
            </div>
        </div>
        @endif
        
        @if($vacancy->job_qualifications)
        <div class="mb-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">Qualifications</h5>
            <div class="prose max-w-none dark:prose-invert">
                {!! nl2br(e($vacancy->job_qualifications)) !!}
            </div>
        </div>
        @endif
        
        @if($vacancy->job_benefits)
        <div class="mb-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">Benefits</h5>
            <div class="prose max-w-none dark:prose-invert">
                {!! nl2br(e($vacancy->job_benefits)) !!}
            </div>
        </div>
        @endif
        
        @if($vacancy->job_salary)
        <div>
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">Salary Information</h5>
            <p>{{ $vacancy->job_salary }}</p>
        </div>
        @endif
    </div>

    <!-- Questions -->
    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-md">
        <h4 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Questions</h4>
        
        @if($vacancy->vacancy_questions->count() > 0)
            <div class="space-y-3">
                @foreach($vacancy->vacancy_questions as $index => $question)
                <div class="border border-slate-200 dark:border-slate-700 rounded-md p-3">
                    <div class="flex justify-between">
                        <div class="flex-1">
                            <h5 class="font-medium">{{ $index + 1 }}. {{ $question->question }}</h5>
                            @if($question->arabic_question)
                            <p class="text-sm text-slate-500 mt-1">{{ $question->arabic_question }}</p>
                            @endif
                        </div>
                        <div class="ml-4 flex space-x-2 items-start">
                            <span class="badge bg-primary-200">{{ ucfirst($question->type) }}</span>
                            @if($question->required)
                            <span class="badge bg-danger-200">Required</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($question->options_array && count($question->options_array) > 0)
                    <div class="mt-2">
                        <h6 class="text-xs font-medium text-slate-500 mb-1">Options:</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach($question->options_array as $option)
                            <span class="badge bg-slate-200 dark:bg-slate-600">{{ $option }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500">No questions have been added for this vacancy.</p>
        @endif
    </div>

    <!-- Time Slots -->
    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-md">
        <h4 class="text-lg font-medium mb-4 text-slate-900 dark:text-white">Time Slots</h4>
        
        @if($vacancy->vacancy_slots->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($vacancy->vacancy_slots as $slot)
                <div class="border border-slate-200 dark:border-slate-700 rounded-md p-3">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                        <div>
                            <p class="font-medium">{{ $slot->date->format('d M Y') }}</p>
                            <p class="text-sm text-slate-500">{{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-500">No time slots have been added for this vacancy.</p>
        @endif
    </div>
</div> 