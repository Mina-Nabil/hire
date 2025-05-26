<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Benefits\Configurations\VacationPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(App::environment('local')) {
        $this->call([
            UsersSeeder::class,
            ChannelSeeder::class,
            OrganizationSeeder::class,
            VacancySeeder::class,
            ApplicantSeeder::class,
            InterviewSeeder::class,
            SalaryGradeSeeder::class,
            VacationPackageSeeder::class,
            EmployeeSeeder::class,
        ]);
        } else if(App::environment('production')) {
            $this->call([
                UsersSeeder::class,
                ChannelSeeder::class,
                VacationPackageSeeder::class,
            ]);
        }
    }
}
