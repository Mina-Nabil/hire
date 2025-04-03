<?php

namespace Database\Factories;

use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hierarchy\Position>
 */
class PositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create some common job titles for positions
        $jobTitles = [
            'Manager', 'Specialist', 'Coordinator', 'Assistant', 'Director',
            'Supervisor', 'Analyst', 'Administrator', 'Officer', 'Engineer'
        ];
        
        $title = fake()->randomElement($jobTitles);
        $name = "{$title} " . fake()->word();
        
        return [
            'department_id' => Department::factory(),
            'name' => $name,
            'arabic_name' => $name . ' (Arabic)', // Placeholder for Arabic name
            'job_description' => fake()->optional(0.9)->paragraph(),
            'arabic_job_description' => fake()->optional(0.9)->paragraph(), // Placeholder for Arabic description
            'job_requirements' => fake()->optional(0.8)->paragraph(),
            'arabic_job_requirements' => fake()->optional(0.8)->paragraph(), // Placeholder for Arabic requirements
            'job_qualifications' => fake()->optional(0.8)->paragraph(),
            'arabic_job_qualifications' => fake()->optional(0.8)->paragraph(), // Placeholder for Arabic qualifications
            'job_benefits' => fake()->optional(0.7)->paragraph(),
            'arabic_job_benefits' => fake()->optional(0.7)->paragraph(), // Placeholder for Arabic benefits
            'parent_id' => null, // By default, no parent
            'employee_id' => null, // By default, no employee assigned
        ];
    }

    /**
     * Define a position with a parent relationship.
     * 
     * @param Position $parent The parent position
     * @return static
     */
    public function withParent(Position $parent): static
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'department_id' => $parent->department_id, // Ensure same department as parent
                'parent_id' => $parent->id,
            ];
        });
    }

    /**
     * Define a manager position (intended to have child positions).
     */
    public function manager(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Department Manager',
                'arabic_name' => 'مدير القسم', // Arabic for Department Manager
                'job_description' => 'Oversees all department operations and staff.',
                'arabic_job_description' => 'يشرف على جميع عمليات وموظفي القسم.',
            ];
        });
    }

    /**
     * Define a developer position.
     */
    public function developer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Software Developer',
                'arabic_name' => 'مطور برمجيات', // Arabic for Software Developer
                'job_description' => 'Designs and develops software applications.',
                'arabic_job_description' => 'يصمم ويطور تطبيقات البرمجيات.',
                'job_requirements' => 'Proficiency in modern programming languages. Experience with software development lifecycle.',
                'arabic_job_requirements' => 'إتقان لغات البرمجة الحديثة. خبرة في دورة حياة تطوير البرمجيات.',
                'job_qualifications' => 'Bachelor\'s degree in Computer Science or related field. 2+ years of development experience.',
                'arabic_job_qualifications' => 'بكالوريوس في علوم الكمبيوتر أو مجال ذي صلة. خبرة في التطوير لمدة عامين على الأقل.',
                'job_benefits' => 'Competitive salary, professional development opportunities, flexible working hours.',
                'arabic_job_benefits' => 'راتب تنافسي، فرص التطوير المهني، ساعات عمل مرنة.',
            ];
        });
    }
    
    /**
     * Define an HR specialist position.
     */
    public function hrSpecialist(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'HR Specialist',
                'arabic_name' => 'أخصائي موارد بشرية', // Arabic for HR Specialist
                'job_description' => 'Handles recruitment, employee relations, and HR policies.',
                'arabic_job_description' => 'يتعامل مع التوظيف وعلاقات الموظفين وسياسات الموارد البشرية.',
            ];
        });
    }
} 