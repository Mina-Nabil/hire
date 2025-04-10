<?php

namespace Database\Seeders;

use App\Models\Hierarchy\Position;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancySlot;
use App\Models\Users\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $positions = Position::all();
        $hrUsers = User::where('type', 'hr')->orWhere('type', 'admin')->get();
        
        if ($positions->isEmpty()) {
            $this->command->error('No positions found. Please run the OrganizationSeeder first.');
            return;
        }
        
        if ($hrUsers->isEmpty()) {
            $this->command->error('No HR or admin users found. Please run the UsersSeeder first.');
            return;
        }
        
        // Create base questions for reuse
        $this->command->info('Creating base questions...');
        $baseQuestions = $this->createBaseQuestions();
        
        // Create vacancies
        $this->command->info('Creating vacancies for various positions...');
        $vacancyCount = min(20, $positions->count()); // Create up to 20 vacancies
        
        $positions = $positions->random($vacancyCount);
        
        foreach ($positions as $position) {
            $this->command->info("Creating vacancy for {$position->name}...");
            
            $status = $faker->randomElement(['open', 'closed']);
            $closingDate = $status === 'open' 
                ? $faker->dateTimeBetween('+2 weeks', '+3 months')->format('Y-m-d')
                : $faker->dateTimeBetween('-3 months', '-1 day')->format('Y-m-d');
            
            $vacancy = Vacancy::create([
                'assigned_to' => $hrUsers->random()->id,
                'hiring_manager_id' => $hrUsers->random()->id,
                'hr_manager_id' => $hrUsers->random()->id,
                'position_id' => $position->id,
                'type' => $faker->randomElement(['full_time', 'part_time', 'temporary']),
                'status' => $status,
                'closing_date' => $closingDate,
                'job_responsibilities' => $this->generateResponsibilities($position->name),
                'arabic_job_responsibilities' => 'المسؤوليات الوظيفية باللغة العربية',
                'job_qualifications' => $this->generateQualifications($position->name),
                'arabic_job_qualifications' => 'المؤهلات المطلوبة باللغة العربية',
                'job_benefits' => $this->generateBenefits(),
                'arabic_job_benefits' => 'المميزات والحوافز باللغة العربية',
                'job_salary' => $faker->randomElement([
                    '5,000 - 7,000 EGP',
                    '7,000 - 10,000 EGP', 
                    '10,000 - 15,000 EGP',
                    '15,000 - 20,000 EGP',
                    '20,000 - 30,000 EGP',
                    'Competitive Salary'
                ]),
            ]);
            
            // Add 3-6 questions to the vacancy
            $questionCount = rand(3, 6);
            $selectedQuestions = $baseQuestions->random($questionCount);
            
            foreach ($selectedQuestions as $baseQuestion) {
                $vacancy->vacancy_questions()->create([
                    'question' => $baseQuestion->question,
                    'arabic_question' => $baseQuestion->arabic_question,
                    'type' => $baseQuestion->type,
                    'required' => $baseQuestion->required,
                    'options' => $baseQuestion->options,
                ]);
            }
            
            // Add interview slots for open vacancies
            if ($status === 'open') {
                $slotCount = rand(3, 8);
                $startDate = Carbon::now()->addDays(7);
                
                for ($i = 0; $i < $slotCount; $i++) {
                    $date = (clone $startDate)->addDays(rand(0, 14))->format('Y-m-d');
                    $startHour = rand(9, 15);
                    $startTime = sprintf("%02d:00", $startHour);
                    $endTime = sprintf("%02d:00", $startHour + 1);
                    
                    VacancySlot::create([
                        'vacancy_id' => $vacancy->id,
                        'date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                }
            }
        }
        
        $this->command->info('Vacancy seeding completed successfully.');
    }
    
    /**
     * Create base questions for vacancies
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function createBaseQuestions()
    {
        BaseQuestion::truncate();
        
        $questions = [
            [
                'question' => 'What are your salary expectations?',
                'arabic_question' => 'ما هي توقعاتك للراتب؟',
                'type' => 'text',
                'required' => true,
                'options' => null,
            ],
            [
                'question' => 'When can you start working?',
                'arabic_question' => 'متى يمكنك بدء العمل؟',
                'type' => 'text',
                'required' => true,
                'options' => null,
            ],
            [
                'question' => 'Are you willing to relocate?',
                'arabic_question' => 'هل أنت مستعد للانتقال؟',
                'type' => 'select',
                'required' => true,
                'options' => "Yes,No,Maybe",
            ],
            [
                'question' => 'What is your highest level of education?',
                'arabic_question' => 'ما هو أعلى مستوى تعليمي لديك؟',
                'type' => 'select',
                'required' => true,
                'options' => "High School,Bachelor's Degree,Master's Degree,Ph.D,Other",
            ],
            [
                'question' => 'How many years of experience do you have in this field?',
                'arabic_question' => 'كم عدد سنوات الخبرة لديك في هذا المجال؟',
                'type' => 'text',
                'required' => true,
                'options' => null,
            ],
            [
                'question' => 'Do you have experience with project management?',
                'arabic_question' => 'هل لديك خبرة في إدارة المشاريع؟',
                'type' => 'select',
                'required' => false,
                'options' => "Yes,No",
            ],
            [
                'question' => 'What programming languages are you proficient in?',
                'arabic_question' => 'ما هي لغات البرمجة التي تتقنها؟',
                'type' => 'text',
                'required' => false,
                'options' => null,
            ],
            [
                'question' => 'What is your availability for work?',
                'arabic_question' => 'ما هي أوقات عملك المتاحة؟',
                'type' => 'select',
                'required' => true,
                'options' => "Full-time,Part-time,Flexible hours,Weekends only",
            ],
            [
                'question' => 'Why do you want to work for our company?',
                'arabic_question' => 'لماذا ترغب في العمل في شركتنا؟',
                'type' => 'textarea',
                'required' => true,
                'options' => null,
            ],
            [
                'question' => 'Describe your leadership style.',
                'arabic_question' => 'صف أسلوب قيادتك.',
                'type' => 'textarea',
                'required' => false,
                'options' => null,
            ],
        ];
        
        foreach ($questions as $question) {
            BaseQuestion::create($question);
        }
        
        return BaseQuestion::all();
    }
    
    /**
     * Generate job responsibilities based on position
     * 
     * @param string $positionName
     * @return string
     */
    private function generateResponsibilities($positionName)
    {
        $commonResponsibilities = [
            "Collaborate with team members to achieve departmental goals.",
            "Prepare regular reports and presentations for management.",
            "Participate in training and development activities.",
            "Ensure compliance with company policies and procedures.",
            "Contribute to improving operational processes and workflows.",
        ];
        
        $positionType = $this->determinePositionType($positionName);
        
        $specificResponsibilities = $this->getSpecificResponsibilities($positionType);
        
        // Combine, shuffle and take 5-7 responsibilities
        $allResponsibilities = array_merge($commonResponsibilities, $specificResponsibilities);
        shuffle($allResponsibilities);
        $selectedResponsibilities = array_slice($allResponsibilities, 0, rand(5, 7));
        
        // Format as bullet points
        return "• " . implode("\n\n• ", $selectedResponsibilities);
    }
    
    /**
     * Generate job qualifications based on position
     * 
     * @param string $positionName
     * @return string
     */
    private function generateQualifications($positionName)
    {
        $commonQualifications = [
            "Bachelor's degree in a relevant field.",
            "Excellent communication and interpersonal skills.",
            "Strong problem-solving abilities.",
            "Proficiency in Microsoft Office suite.",
            "Ability to work in a team environment.",
        ];
        
        $positionType = $this->determinePositionType($positionName);
        
        $specificQualifications = $this->getSpecificQualifications($positionType);
        
        // Combine, shuffle and take 4-6 qualifications
        $allQualifications = array_merge($commonQualifications, $specificQualifications);
        shuffle($allQualifications);
        $selectedQualifications = array_slice($allQualifications, 0, rand(4, 6));
        
        // Format as bullet points
        return "• " . implode("\n\n• ", $selectedQualifications);
    }
    
    /**
     * Generate job benefits
     * 
     * @return string
     */
    private function generateBenefits()
    {
        $allBenefits = [
            "Competitive salary package",
            "Health insurance coverage",
            "Annual performance bonus",
            "Flexible working hours",
            "Remote work options",
            "Professional development opportunities",
            "Career advancement paths",
            "Paid time off and vacation days",
            "Maternity and paternity leave",
            "Employee wellness programs",
            "Team building activities",
            "Modern office environment",
            "Transportation allowance",
            "Meal allowance",
            "Mobile phone allowance",
            "Employee recognition programs",
        ];
        
        shuffle($allBenefits);
        $selectedBenefits = array_slice($allBenefits, 0, rand(4, 6));
        
        // Format as bullet points
        return "• " . implode("\n\n• ", $selectedBenefits);
    }
    
    /**
     * Determine position type from position name
     * 
     * @param string $positionName
     * @return string
     */
    private function determinePositionType($positionName)
    {
        $positionName = strtolower($positionName);
        
        if (str_contains($positionName, 'develop') || str_contains($positionName, 'engineer') || str_contains($positionName, 'program')) {
            return 'technical';
        }
        
        if (str_contains($positionName, 'manager') || str_contains($positionName, 'director') || str_contains($positionName, 'lead')) {
            return 'management';
        }
        
        if (str_contains($positionName, 'market') || str_contains($positionName, 'sales') || str_contains($positionName, 'relation')) {
            return 'marketing';
        }
        
        if (str_contains($positionName, 'finance') || str_contains($positionName, 'account') || str_contains($positionName, 'budget')) {
            return 'finance';
        }
        
        if (str_contains($positionName, 'hr') || str_contains($positionName, 'human resource') || str_contains($positionName, 'recruit')) {
            return 'hr';
        }
        
        return 'general';
    }
    
    /**
     * Get specific responsibilities based on position type
     * 
     * @param string $positionType
     * @return array
     */
    private function getSpecificResponsibilities($positionType)
    {
        switch ($positionType) {
            case 'technical':
                return [
                    "Develop and maintain software applications according to business requirements.",
                    "Write clean, maintainable, and efficient code.",
                    "Perform code reviews and ensure code quality.",
                    "Debug and resolve technical issues and software defects.",
                    "Implement security and data protection measures.",
                    "Work with APIs and integrate third-party services.",
                    "Optimize applications for maximum speed and scalability.",
                    "Design and implement database structures.",
                ];
                
            case 'management':
                return [
                    "Lead and manage a team of professionals to achieve business objectives.",
                    "Develop and implement strategic plans and initiatives.",
                    "Set performance goals and conduct regular evaluations.",
                    "Mentor and develop team members through coaching and feedback.",
                    "Manage budgets and resources efficiently.",
                    "Represent the department in cross-functional meetings.",
                    "Build and maintain relationships with key stakeholders.",
                    "Drive innovation and continuous improvement within the team.",
                ];
                
            case 'marketing':
                return [
                    "Develop and implement marketing strategies to promote the company's products or services.",
                    "Create and manage content for various marketing channels.",
                    "Analyze market trends, competition, and customer behavior.",
                    "Manage social media accounts and digital marketing campaigns.",
                    "Measure and report on the performance of marketing initiatives.",
                    "Collaborate with design teams to create marketing materials.",
                    "Organize events and promotional activities.",
                    "Develop and maintain relationships with media and industry partners.",
                ];
                
            case 'finance':
                return [
                    "Prepare financial statements and reports.",
                    "Manage budgeting and forecasting processes.",
                    "Ensure compliance with financial regulations and tax requirements.",
                    "Analyze financial data and provide insights to management.",
                    "Manage accounts payable and receivable.",
                    "Handle payroll processing and financial record-keeping.",
                    "Conduct financial audits and risk assessments.",
                    "Develop and implement financial policies and procedures.",
                ];
                
            case 'hr':
                return [
                    "Recruit, screen, and interview candidates for open positions.",
                    "Manage employee onboarding and offboarding processes.",
                    "Administer employee benefits and compensation programs.",
                    "Handle employee relations issues and conflict resolution.",
                    "Develop and implement HR policies and procedures.",
                    "Maintain personnel records and ensure data confidentiality.",
                    "Coordinate training and development programs.",
                    "Support performance management processes.",
                ];
                
            default: // general
                return [
                    "Support daily office operations and administrative tasks.",
                    "Handle customer inquiries and provide appropriate solutions.",
                    "Maintain and update databases and filing systems.",
                    "Coordinate schedules, meetings, and appointments.",
                    "Prepare correspondence, memos, and other documents.",
                    "Assist with special projects and events as needed.",
                    "Maintain office supplies inventory and place orders when necessary.",
                    "Provide support to other departments as required.",
                ];
        }
    }
    
    /**
     * Get specific qualifications based on position type
     * 
     * @param string $positionType
     * @return array
     */
    private function getSpecificQualifications($positionType)
    {
        switch ($positionType) {
            case 'technical':
                return [
                    "Experience with programming languages such as Java, Python, or JavaScript.",
                    "Knowledge of software development methodologies like Agile or Scrum.",
                    "Familiarity with version control systems like Git.",
                    "Understanding of database systems and SQL.",
                    "Experience with cloud platforms (AWS, Azure, or GCP).",
                    "Knowledge of web development frameworks.",
                    "Understanding of software testing and debugging.",
                    "Experience with CI/CD pipelines.",
                ];
                
            case 'management':
                return [
                    "Proven leadership experience with a track record of success.",
                    "Experience in strategic planning and execution.",
                    "Strong decision-making and problem-solving abilities.",
                    "Experience in budget management and resource allocation.",
                    "Knowledge of performance management techniques.",
                    "Excellent negotiation and conflict resolution skills.",
                    "Change management experience.",
                    "Business acumen and industry knowledge.",
                ];
                
            case 'marketing':
                return [
                    "Experience in creating and implementing marketing campaigns.",
                    "Knowledge of SEO, SEM, and digital marketing strategies.",
                    "Experience with social media management and analytics tools.",
                    "Content creation and copywriting skills.",
                    "Understanding of market research and data analysis.",
                    "Experience with CRM systems and marketing software.",
                    "Creative thinking and innovative approach to marketing.",
                    "Experience with brand development and management.",
                ];
                
            case 'finance':
                return [
                    "Understanding of financial statements and reporting standards.",
                    "Experience with financial analysis and forecasting.",
                    "Knowledge of accounting principles and practices.",
                    "Experience with financial software and ERP systems.",
                    "Attention to detail and high level of accuracy.",
                    "Knowledge of tax regulations and compliance requirements.",
                    "Experience with budget preparation and control.",
                    "Understanding of risk management principles.",
                ];
                
            case 'hr':
                return [
                    "Knowledge of HR best practices and employment laws.",
                    "Experience with recruitment and selection processes.",
                    "Understanding of compensation and benefits administration.",
                    "Experience with HRIS and applicant tracking systems.",
                    "Strong interpersonal and counseling skills.",
                    "Knowledge of training and development methodologies.",
                    "Experience with performance management systems.",
                    "Understanding of employee relations and conflict resolution.",
                ];
                
            default: // general
                return [
                    "Organizational and time management skills.",
                    "Customer service orientation and skills.",
                    "Attention to detail and accuracy in work.",
                    "Ability to prioritize and handle multiple tasks simultaneously.",
                    "Initiative and self-motivation.",
                    "Adaptability and flexibility in a changing environment.",
                    "Professional demeanor and ethical conduct.",
                    "Analytical thinking and problem-solving skills.",
                ];
        }
    }
} 