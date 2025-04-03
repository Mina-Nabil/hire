<?php

namespace Database\Factories;

use App\Models\Hierarchy\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hierarchy\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate department with unique prefix code
        return [
            'name' => fake()->unique()->word() . ' Department',
            'prefix_code' => strtoupper(fake()->unique()->lexify('???')),
            'desc' => fake()->optional(0.7)->sentence(),
        ];
    }

    /**
     * Define an IT department.
     */
    public function it(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Information Technology',
            'prefix_code' => 'IT',
            'desc' => 'Department responsible for technology infrastructure and systems.',
        ]);
    }

    /**
     * Define an HR department.
     */
    public function hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Human Resources',
            'prefix_code' => 'HR',
            'desc' => 'Department responsible for employee recruitment, onboarding and management.',
        ]);
    }

    /**
     * Define a Finance department.
     */
    public function finance(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Finance',
            'prefix_code' => 'FIN',
            'desc' => 'Department responsible for financial management and accounting.',
        ]);
    }
} 