<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            ChannelSeeder::class,
            OrganizationSeeder::class,
            ChannelSeeder::class,
            VacancySeeder::class,
            ApplicantSeeder::class,
            InterviewSeeder::class,
            EmployeeSeeder::class,
            BenefitPackageSeeder::class,
        ]);
    }
}
