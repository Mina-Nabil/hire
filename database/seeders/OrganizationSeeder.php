<?php

namespace Database\Seeders;

use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Position::query()->delete();
        Department::query()->delete();
        
        // Create departments
        $departments = $this->createDepartments();
        
        // Create CEO position
        $ceo = $this->createCEO();
        
        // Create executive team (C-suite, reporting to CEO)
        $executiveTeam = $this->createExecutiveTeam($departments, $ceo);
        
        // Create middle management (25-40 positions reporting to executives)
        $middleManagement = $this->createMiddleManagement($departments, $executiveTeam);
        
        // Create staff positions (5 positions under each middle manager)
        $this->createStaffPositions($departments, $middleManagement);
        
        // Display summary
        $this->displaySummary();
    }
    
    /**
     * Create departments
     * 
     * @return Collection
     */
    private function createDepartments(): Collection
    {
        $departments = collect([
            Department::factory()->state([
                'name' => 'Executive Office',
                'prefix_code' => 'EXE',
                'desc' => 'Executive leadership and administration.'
            ])->create(),
            Department::factory()->it()->create(),
            Department::factory()->hr()->create(),
            Department::factory()->finance()->create(),
            Department::factory()->state([
                'name' => 'Marketing',
                'prefix_code' => 'MKT',
                'desc' => 'Department responsible for marketing and brand management.'
            ])->create(),
            Department::factory()->state([
                'name' => 'Operations',
                'prefix_code' => 'OPS',
                'desc' => 'Department responsible for day-to-day operations.'
            ])->create(),
            Department::factory()->state([
                'name' => 'Research & Development',
                'prefix_code' => 'R&D',
                'desc' => 'Department responsible for innovation and product development.'
            ])->create(),
            Department::factory()->state([
                'name' => 'Sales',
                'prefix_code' => 'SLS',
                'desc' => 'Department responsible for sales and revenue generation.'
            ])->create(),
            Department::factory()->state([
                'name' => 'Customer Service',
                'prefix_code' => 'CS',
                'desc' => 'Department responsible for customer support and experience.'
            ])->create(),
            Department::factory()->state([
                'name' => 'Legal',
                'prefix_code' => 'LGL',
                'desc' => 'Department responsible for legal matters and compliance.'
            ])->create()
        ]);
        
        return $departments;
    }
    
    /**
     * Create CEO position
     * 
     * @return Position
     */
    private function createCEO(): Position
    {
        $executiveDept = Department::where('prefix_code', 'EXE')->first();
        
        $ceo = Position::factory()
            ->for($executiveDept)
            ->state([
                'name' => 'Chief Executive Officer',
                'arabic_name' => 'الرئيس التنفيذي',
                'job_description' => 'Provides overall leadership and strategic direction for the entire organization.',
                'arabic_job_description' => 'يوفر القيادة العامة والتوجيه الاستراتيجي للمؤسسة بأكملها.',
                'parent_id' => null
            ])
            ->create();
            
        $this->command->info("Created CEO position");
        
        return $ceo;
    }
    
    /**
     * Create executive team (C-suite)
     * 
     * @param Collection $departments
     * @param Position $ceo
     * @return Collection
     */
    private function createExecutiveTeam(Collection $departments, Position $ceo): Collection
    {
        $executiveTeam = collect();
        
        // Create C-suite positions (reporting to CEO)
        $cSuiteRoles = [
            ['dept' => 'Finance', 'title' => 'Chief Financial Officer', 'arabic' => 'المدير المالي', 'code' => 'CFO'],
            ['dept' => 'Operations', 'title' => 'Chief Operations Officer', 'arabic' => 'مدير العمليات', 'code' => 'COO'],
            ['dept' => 'Information Technology', 'title' => 'Chief Technology Officer', 'arabic' => 'مدير تكنولوجيا المعلومات', 'code' => 'CTO'],
            ['dept' => 'Human Resources', 'title' => 'Chief People Officer', 'arabic' => 'مدير الموارد البشرية', 'code' => 'CPO'],
            ['dept' => 'Marketing', 'title' => 'Chief Marketing Officer', 'arabic' => 'مدير التسويق', 'code' => 'CMO']
        ];
        
        foreach ($cSuiteRoles as $role) {
            $department = $departments->where('name', $role['dept'])->first() ?? 
                          $departments->where('name', 'like', '%' . $role['dept'] . '%')->first() ??
                          $departments->first();
            
            $executive = Position::factory()
                ->for($department)
                ->state([
                    'name' => $role['title'],
                    'arabic_name' => $role['arabic'],
                    'job_description' => 'Executive leadership for the ' . $department->name . ' department, reporting to the CEO.',
                    'arabic_job_description' => 'القيادة التنفيذية لقسم ' . $department->name . '، والإبلاغ المباشر للرئيس التنفيذي.',
                    'parent_id' => $ceo->id
                ])
                ->create();
                
            $executiveTeam->push($executive);
        }
        
        $this->command->info("Created {$executiveTeam->count()} executive positions reporting to CEO");
        
        return $executiveTeam;
    }
    
    /**
     * Create middle management positions
     * 
     * @param Collection $departments
     * @param Collection $executiveTeam
     * @return Collection
     */
    private function createMiddleManagement(Collection $departments, Collection $executiveTeam): Collection
    {
        $middleManagement = collect();
        $middleManagerCount = rand(25, 40);
        $executiveCount = $executiveTeam->count();
        
        // Calculate how many managers per executive (distribute evenly)
        $managersPerExecutive = ceil($middleManagerCount / $executiveCount);
        
        foreach ($executiveTeam as $executive) {
            $department = $executive->department;
            $managerCount = min($managersPerExecutive, $middleManagerCount - $middleManagement->count());
            
            for ($i = 0; $i < $managerCount; $i++) {
                // Determine manager title based on department
                $managerTitle = $this->getManagerTitleForDepartment($department, $i);
                
                $manager = Position::factory()
                    ->for($department)
                    ->state([
                        'name' => $managerTitle,
                        'arabic_name' => 'مدير ' . $managerTitle,
                        'job_description' => 'Manages a division within the ' . $department->name . ' department, reporting to the ' . $executive->name . '.',
                        'parent_id' => $executive->id
                    ])
                    ->create();
                    
                $middleManagement->push($manager);
                
                if ($middleManagement->count() >= $middleManagerCount) {
                    break; // Reached the maximum count
                }
            }
            
            if ($middleManagement->count() >= $middleManagerCount) {
                break; // Reached the maximum count
            }
        }
        
        $this->command->info("Created {$middleManagement->count()} middle management positions");
        
        return $middleManagement;
    }
    
    /**
     * Create staff positions under middle management
     * 
     * @param Collection $departments
     * @param Collection $middleManagement
     */
    private function createStaffPositions(Collection $departments, Collection $middleManagement): void
    {
        $totalStaffCount = 0;
        
        foreach ($middleManagement as $manager) {
            $department = $manager->department;
            $staffCount = 5; // Exactly 5 positions under each middle manager
            
            for ($i = 0; $i < $staffCount; $i++) {
                $staffTitle = $this->getStaffTitleForDepartment($department, $i);
                
                $staff = Position::factory()
                    ->for($department)
                    ->state([
                        'name' => $staffTitle,
                        'arabic_name' => $staffTitle . ' (Arabic)',
                        'job_description' => 'Responsible for ' . strtolower($staffTitle) . ' duties within the ' . $department->name . ' department.',
                        'parent_id' => $manager->id
                    ])
                    ->create();
            }
            
            $totalStaffCount += $staffCount;
        }
        
        $this->command->info("Created {$totalStaffCount} staff positions");
    }
    
    /**
     * Get an appropriate manager title based on department
     * 
     * @param Department $department
     * @param int $index
     * @return string
     */
    private function getManagerTitleForDepartment(Department $department, int $index): string
    {
        $departmentTitles = [
            'Executive Office' => [
                'Executive Director',
                'Board Secretary',
                'Chief of Staff',
            ],
            'Information Technology' => [
                'IT Infrastructure Manager',
                'Software Development Manager',
                'IT Security Manager',
                'Data Analytics Manager',
                'IT Support Manager',
                'Cloud Services Manager',
            ],
            'Human Resources' => [
                'Recruitment Manager',
                'Training & Development Manager',
                'Employee Relations Manager',
                'Compensation & Benefits Manager',
                'HR Operations Manager',
            ],
            'Finance' => [
                'Financial Planning Manager',
                'Accounting Manager',
                'Treasury Manager',
                'Payroll Manager',
                'Tax Manager',
                'Audit Manager',
            ],
            'Marketing' => [
                'Brand Manager',
                'Digital Marketing Manager',
                'Market Research Manager',
                'Social Media Manager',
                'Content Marketing Manager',
                'Product Marketing Manager',
            ],
            'Operations' => [
                'Production Manager',
                'Quality Control Manager',
                'Logistics Manager',
                'Supply Chain Manager',
                'Facilities Manager',
            ],
            'Research & Development' => [
                'Research Manager',
                'Product Development Manager',
                'Innovation Manager',
                'Testing Manager',
                'Design Manager',
            ],
            'Sales' => [
                'Regional Sales Manager',
                'Key Account Manager',
                'Sales Operations Manager',
                'Inside Sales Manager',
                'Business Development Manager',
            ],
            'Customer Service' => [
                'Customer Support Manager',
                'Customer Experience Manager',
                'Call Center Manager',
                'Client Relations Manager',
            ],
            'Legal' => [
                'Legal Affairs Manager',
                'Compliance Manager',
                'Contract Manager',
                'Intellectual Property Manager',
            ],
        ];
        
        $defaultTitles = [
            'Regional Manager',
            'Department Manager',
            'Senior Manager',
            'Division Manager',
            'Area Manager',
        ];
        
        $titles = $departmentTitles[$department->name] ?? $defaultTitles;
        
        return $titles[$index % count($titles)];
    }
    
    /**
     * Get an appropriate staff title based on department
     * 
     * @param Department $department
     * @param int $index
     * @return string
     */
    private function getStaffTitleForDepartment(Department $department, int $index): string
    {
        $departmentTitles = [
            'Executive Office' => [
                'Executive Assistant',
                'Administrative Coordinator',
                'Executive Coordinator',
                'Special Projects Coordinator',
                'Office Manager',
            ],
            'Information Technology' => [
                'Software Developer',
                'Systems Administrator',
                'Network Engineer',
                'Database Administrator',
                'IT Support Specialist',
                'Security Analyst',
                'QA Tester',
                'Business Analyst',
                'DevOps Engineer',
                'UI/UX Designer',
            ],
            'Human Resources' => [
                'HR Specialist',
                'Recruitment Specialist',
                'Training Coordinator',
                'HR Analyst',
                'Benefits Administrator',
                'Employee Relations Specialist',
                'Onboarding Specialist',
            ],
            'Finance' => [
                'Financial Analyst',
                'Accountant',
                'Budget Analyst',
                'Payroll Specialist',
                'Accounts Payable Specialist',
                'Accounts Receivable Specialist',
                'Tax Specialist',
            ],
            'Marketing' => [
                'Marketing Specialist',
                'Content Creator',
                'Graphic Designer',
                'SEO Specialist',
                'Email Marketing Specialist',
                'Social Media Coordinator',
                'Market Researcher',
            ],
            'Operations' => [
                'Operations Analyst',
                'Process Improvement Specialist',
                'Quality Assurance Specialist',
                'Logistics Coordinator',
                'Supply Chain Analyst',
                'Inventory Specialist',
            ],
            'Research & Development' => [
                'Research Scientist',
                'Product Developer',
                'Laboratory Technician',
                'Design Engineer',
                'Testing Specialist',
                'Documentation Specialist',
            ],
            'Sales' => [
                'Sales Representative',
                'Account Executive',
                'Sales Analyst',
                'Sales Coordinator',
                'Business Development Representative',
                'Sales Support Specialist',
            ],
            'Customer Service' => [
                'Customer Service Representative',
                'Support Specialist',
                'Customer Success Specialist',
                'Call Center Agent',
                'Client Relationship Coordinator',
            ],
            'Legal' => [
                'Legal Assistant',
                'Paralegal',
                'Contract Specialist',
                'Legal Researcher',
                'Compliance Specialist',
            ],
        ];
        
        $defaultTitles = [
            'Specialist',
            'Analyst',
            'Coordinator',
            'Associate',
            'Assistant',
            'Administrator',
            'Officer',
        ];
        
        $titles = $departmentTitles[$department->name] ?? $defaultTitles;
        
        return $titles[$index % count($titles)];
    }
    
    /**
     * Display summary of seeded data
     */
    private function displaySummary(): void
    {
        $totalDepartments = Department::count();
        $totalPositions = Position::count();
        $ceoCount = Position::whereNull('parent_id')->count();
        $executiveCount = Position::whereHas('parent', function($q) {
            $q->whereNull('parent_id');
        })->count();
        $middleManagementCount = Position::whereHas('parent', function($q) {
            $q->whereHas('parent', function($q2) {
                $q2->whereNull('parent_id');
            });
        })->count();
        $staffCount = $totalPositions - $ceoCount - $executiveCount - $middleManagementCount;
        
        $this->command->info('Organization Seeding Completed:');
        $this->command->info("- Created $totalDepartments departments");
        $this->command->info("- Created $totalPositions positions:");
        $this->command->info("  - 1 CEO");
        $this->command->info("  - $executiveCount executives (C-suite)");
        $this->command->info("  - $middleManagementCount middle managers");
        $this->command->info("  - $staffCount staff positions");
        
        foreach (Department::all() as $department) {
            $positionsCount = Position::where('department_id', $department->id)->count();
            $this->command->info("  - {$department->name}: $positionsCount positions");
        }
        
        // Display hierarchy depth
        $this->analyzeHierarchyDepth();
    }
    
    /**
     * Analyze and display hierarchy depth information
     */
    private function analyzeHierarchyDepth(): void
    {
        $rootPositions = Position::whereNull('parent_id')->get();
        $maxDepth = 0;
        
        foreach ($rootPositions as $rootPosition) {
            $depth = $this->calculateMaxDepth($rootPosition);
            $maxDepth = max($maxDepth, $depth);
        }
        
        $this->command->info("- Maximum hierarchy depth: " . $maxDepth . " levels");
    }
    
    /**
     * Calculate maximum depth of a position hierarchy
     * 
     * @param Position $position
     * @param int $currentDepth
     * @return int
     */
    private function calculateMaxDepth(Position $position, int $currentDepth = 0): int
    {
        $children = Position::where('parent_id', $position->id)->get();
        
        if ($children->isEmpty()) {
            return $currentDepth;
        }
        
        $maxChildDepth = 0;
        foreach ($children as $child) {
            $childDepth = $this->calculateMaxDepth($child, $currentDepth + 1);
            $maxChildDepth = max($maxChildDepth, $childDepth);
        }
        
        return $maxChildDepth;
    }
}
