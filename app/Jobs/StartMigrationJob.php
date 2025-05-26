<?php

namespace App\Jobs;

use App\Services\MigrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StartMigrationJob implements ShouldQueue
{
    use Queueable;

    public array $locations;
    public array $departments;
    public array $employees;
    public array $salary_grades;
    public array $positions;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $locations,
        array $departments,
        array $employees,
        array $salary_grades,
        array $positions
    ) {
        $this->locations = $locations;
        $this->departments = $departments;
        $this->employees = $employees;
        $this->salary_grades = $salary_grades;
        $this->positions = $positions;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MigrationService::importData($this->locations, $this->departments, $this->employees, $this->salary_grades, $this->positions);
    }
}
