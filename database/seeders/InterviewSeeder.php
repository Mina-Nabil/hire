<?php

namespace Database\Seeders;

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Users\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        
        // Get applications that can be interviewed
        $applications = Application::with(['vacancy', 'applicant'])
            ->whereIn('status', ['pending', 'shortlisted', 'interview'])
            ->get();
            
        if ($applications->isEmpty()) {
            $this->command->error('No suitable applications found. Please run ApplicantSeeder first.');
            return;
        }
        
        // Get HR and admin users who can conduct interviews
        $interviewers = User::where('type', 'hr')->orWhere('type', 'admin')->get();
        
        if ($interviewers->isEmpty()) {
            $this->command->error('No HR or admin users found. Please run UsersSeeder first.');
            return;
        }
        
        $this->command->info('Creating interviews for suitable applications...');
        
        // Select approximately 60% of applications for interviews
        $applicationsForInterviews = $applications->random(max(1, intval($applications->count() * 0.6)));
        $this->command->info('Selected ' . $applicationsForInterviews->count() . ' applications for interviews');
        
        $now = Carbon::now();
        $interviewCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;
        $pendingCount = 0;
        $scheduledCount = 0;
        
        foreach ($applicationsForInterviews as $application) {
            $assignedInterviewer = $interviewers->random();
            
            // Determine interview date (past, current, or future)
            $dateProbability = $faker->randomElement([
                'past' => 40,    // 40% past interviews
                'today' => 10,   // 10% today
                'future' => 50,  // 50% future interviews
            ]);
            
            if ($dateProbability === 'past') {
                $interviewDate = $faker->dateTimeBetween('-3 months', '-1 day');
            } elseif ($dateProbability === 'today') {
                $interviewDate = $faker->dateTimeBetween('-8 hours', '+8 hours');
            } else {
                $interviewDate = $faker->dateTimeBetween('+1 day', '+2 months');
            }
            
            // Generate interview details
            $interviewType = $faker->randomElement(Interview::INTERVIEW_TYPES);
            $location = $interviewType === Interview::TYPE_IN_PERSON 
                ? $faker->randomElement(['Main Office - Meeting Room A', 'Main Office - Meeting Room B', 'Branch Office - Conference Room', 'Head Office - Interview Room 1'])
                : null;
            $zoomLink = $interviewType === Interview::TYPE_ONLINE 
                ? 'https://zoom.us/j/' . $faker->numerify('##########')
                : null;
            
            // Determine status based on date
            $status = $this->determineStatus($interviewDate, $now);
            
            // Create the interview
            $interview = Interview::create([
                'application_id' => $application->id,
                'user_id' => $assignedInterviewer->id,
                'date' => $interviewDate,
                'type' => $interviewType,
                'location' => $location,
                'zoom_link' => $zoomLink,
                'status' => $status,
            ]);
            
            // Update application status based on interview status
            if ($status === Interview::STATUS_COMPLETED) {
                $application->status = 'interview';
                $application->save();
                $completedCount++;
                
                // Add feedback for completed interviews
                $this->addInterviewFeedback($interview, $faker);
            } elseif ($status === Interview::STATUS_CANCELLED) {
                $cancelledCount++;
                
                // Add cancellation note
                $interview->addNote(
                    'Cancellation Reason',
                    $faker->randomElement([
                        'Candidate withdrew application',
                        'Position requirements changed',
                        'Scheduling conflict',
                        'Interviewer unavailable',
                        'Position filled by another candidate',
                        'Internal restructuring'
                    ])
                );
            } elseif ($status === Interview::STATUS_PENDING) {
                $pendingCount++;
            } elseif ($status === Interview::STATUS_SCHEDULED) {
                $application->status = 'interview';
                $application->save();
                $scheduledCount++;
            }
            
            // Assign additional interviewers (1-3)
            if ($status !== Interview::STATUS_CANCELLED) {
                $additionalInterviewers = $interviewers->where('id', '!=', $assignedInterviewer->id)
                    ->random(min($interviewers->count() - 1, rand(1, 3)));
                
                foreach ($additionalInterviewers as $interviewer) {
                    $interview->interviewers()->create([
                        'user_id' => $interviewer->id
                    ]);
                }
            }
            
            // Add some interview notes (30% chance)
            if ($faker->boolean(30) && $status !== Interview::STATUS_PENDING) {
                $interview->addNote(
                    $faker->randomElement(['Pre-interview Note', 'Resume Review', 'Initial Impression', 'Background Check']),
                    $faker->paragraph(2)
                );
            }
            
            $interviewCount++;
        }
        
        $this->command->info("Successfully created {$interviewCount} interviews");
        $this->command->info("- Completed: {$completedCount}");
        $this->command->info("- Scheduled: {$scheduledCount}");
        $this->command->info("- Pending: {$pendingCount}");
        $this->command->info("- Cancelled: {$cancelledCount}");
    }
    
    /**
     * Determine interview status based on date
     * 
     * @param \DateTime $interviewDate
     * @param \Carbon\Carbon $now
     * @return string
     */
    private function determineStatus($interviewDate, $now)
    {
        $faker = Factory::create();
        $interviewDateTime = Carbon::instance($interviewDate);
        
        if ($interviewDateTime->lt($now)) {
            // Past interview
            if ($faker->boolean(80)) {
                return Interview::STATUS_COMPLETED;
            } else {
                return Interview::STATUS_CANCELLED;
            }
        } elseif ($interviewDateTime->isSameDay($now)) {
            // Today
            if ($interviewDateTime->lt($now)) {
                return Interview::STATUS_COMPLETED;
            } elseif ($interviewDateTime->diffInHours($now) < 1) {
                return Interview::STATUS_SCHEDULED;
            } else {
                return Interview::STATUS_PENDING;
            }
        } else {
            // Future interview
            if ($faker->boolean(10)) {
                return Interview::STATUS_CANCELLED;
            } else {
                return Interview::STATUS_PENDING;
            }
        }
    }
    
    /**
     * Add feedback to completed interviews
     * 
     * @param Interview $interview
     * @param \Faker\Generator $faker
     * @return void
     */
    private function addInterviewFeedback($interview, $faker)
    {
        $results = ['Passed', 'Failed', 'On Hold'];
        $resultWeights = [70, 20, 10]; // 70% pass, 20% fail, 10% on hold
        
        $result = $faker->randomElement(array_map(function ($element, $weight) {
            return array_fill(0, $weight, $element);
        }, $results, $resultWeights));
        
        $rating = $result === 'Passed' 
            ? $faker->numberBetween(7, 10) 
            : ($result === 'On Hold' ? $faker->numberBetween(5, 7) : $faker->numberBetween(1, 5));
        
        $strengths = $this->generateStrengths($faker);
        $weaknesses = $this->generateWeaknesses($faker);
        
        $interview->result = $result;
        $interview->rating = $rating;
        $interview->strengths = $strengths;
        $interview->weaknesses = $weaknesses;
        $interview->feedback = $faker->paragraph(3);
        $interview->next_step = $this->determineNextStep($result, $faker);
        $interview->save();
        
        // Update application status based on result
        $application = $interview->application;
        if ($result === 'Passed') {
            $application->status = 'interview';
        } elseif ($result === 'Failed') {
            $application->status = 'rejected';
        }
        $application->save();
    }
    
    /**
     * Generate candidate strengths
     * 
     * @param \Faker\Generator $faker
     * @return string
     */
    private function generateStrengths($faker)
    {
        $allStrengths = [
            "Strong technical knowledge and expertise",
            "Excellent communication and interpersonal skills",
            "Proven problem-solving abilities",
            "Demonstrated leadership experience",
            "Good time management and organizational skills",
            "Relevant industry experience",
            "Strong analytical thinking",
            "Team-oriented approach",
            "Initiative and self-motivation",
            "Adaptability and flexibility",
            "Creative thinking and innovation",
            "Attention to detail",
            "Customer-focused mindset",
            "Professional presentation and demeanor",
            "Relevant certifications and qualifications",
            "Experience with specific tools and technologies",
            "Strong project management skills",
            "Good cultural fit with organization",
            "Willingness to learn and grow",
            "Positive attitude and enthusiasm"
        ];
        
        // Select 3-5 strengths
        $count = $faker->numberBetween(3, 5);
        $selectedStrengths = $faker->randomElements($allStrengths, $count);
        
        return implode("\n\n", $selectedStrengths);
    }
    
    /**
     * Generate candidate weaknesses
     * 
     * @param \Faker\Generator $faker
     * @return string
     */
    private function generateWeaknesses($faker)
    {
        $allWeaknesses = [
            "Limited experience in some required areas",
            "Communication could be more concise",
            "Could improve technical knowledge in specific areas",
            "Limited leadership experience",
            "May need support with time management",
            "Limited exposure to our specific industry",
            "Could enhance problem-solving approach",
            "May need additional training on certain tools",
            "Limited experience with team collaboration",
            "Could benefit from more proactive approach",
            "May need to develop more strategic thinking",
            "Attention to detail could be improved",
            "Limited experience with client-facing roles",
            "May need support with prioritization",
            "Limited experience with specific methodologies",
            "Could improve confidence in presentations",
            "May need mentoring in career development",
            "Limited experience with remote work",
            "Could enhance documentation skills",
            "May benefit from constructive feedback"
        ];
        
        // Select 2-3 weaknesses
        $count = $faker->numberBetween(2, 3);
        $selectedWeaknesses = $faker->randomElements($allWeaknesses, $count);
        
        return implode("\n\n", $selectedWeaknesses);
    }
    
    /**
     * Determine next step based on interview result
     * 
     * @param string $result
     * @param \Faker\Generator $faker
     * @return string
     */
    private function determineNextStep($result, $faker)
    {
        if ($result === 'Passed') {
            return $faker->randomElement([
                'Proceed to next interview round',
                'Technical assessment',
                'Meet with team members',
                'Reference check',
                'Final interview with senior management',
                'Prepare job offer'
            ]);
        } elseif ($result === 'On Hold') {
            return $faker->randomElement([
                'Review against other candidates',
                'Additional skills assessment',
                'Wait for more applications',
                'Consider for different position',
                'Re-evaluate in 2 weeks'
            ]);
        } else { // Failed
            return $faker->randomElement([
                'Reject application with feedback',
                'Keep resume on file for future opportunities',
                'Suggest applying for more suitable positions',
                'No further action'
            ]);
        }
    }
} 