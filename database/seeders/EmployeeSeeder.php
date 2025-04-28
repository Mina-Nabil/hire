<?php

namespace Database\Seeders;

use App\Models\Personel\Employee;
use App\Models\Base\City;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a city for birth place
        $city = City::first();
        
        if (!$city) {
            // If no city exists, create one
            $city = City::create([
                'name' => 'Istanbul',
                'country_id' => 1, // Assuming Turkey has ID 1
            ]);
        }

        // Create 5 employees with realistic data
        $employees = [
            [
                'user_id' => 1,
                'created_by' => 1,
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet.yilmaz@example.com',
                'phone' => '+90 532 123 4567',
                'address' => 'Kadıköy, Istanbul',
                'nationality' => 'Turkish',
                'gender' => 'male',
                'birth_date' => Carbon::createFromDate(1985, 6, 15),
                'birth_place_id' => $city->id,
                'license_required' => true,
                'employment_date' => Carbon::createFromDate(2018, 3, 10),
            ],
            [
                'user_id' => 1,
                'created_by' => 1,
                'name' => 'Ayşe Demir',
                'email' => 'ayse.demir@example.com',
                'phone' => '+90 533 234 5678',
                'address' => 'Beşiktaş, Istanbul',
                'nationality' => 'Turkish',
                'gender' => 'female',
                'birth_date' => Carbon::createFromDate(1990, 8, 22),
                'birth_place_id' => $city->id,
                'license_required' => false,
                'employment_date' => Carbon::createFromDate(2019, 5, 15),
            ],
            [
                'user_id' => 1,
                'created_by' => 1,
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet.kaya@example.com',
                'phone' => '+90 535 345 6789',
                'address' => 'Üsküdar, Istanbul',
                'nationality' => 'Turkish',
                'gender' => 'male',
                'birth_date' => Carbon::createFromDate(1988, 4, 30),
                'birth_place_id' => $city->id,
                'license_required' => true,
                'employment_date' => Carbon::createFromDate(2020, 1, 20),
            ],
            [
                'user_id' => 1,
                'created_by' => 1,
                'name' => 'Zeynep Şahin',
                'email' => 'zeynep.sahin@example.com',
                'phone' => '+90 536 456 7890',
                'address' => 'Şişli, Istanbul',
                'nationality' => 'Turkish',
                'gender' => 'female',
                'birth_date' => Carbon::createFromDate(1992, 11, 5),
                'birth_place_id' => $city->id,
                'license_required' => false,
                'employment_date' => Carbon::createFromDate(2021, 7, 12),
            ],
            [
                'user_id' => 1,
                'created_by' => 1,
                'name' => 'Ali Öztürk',
                'email' => 'ali.ozturk@example.com',
                'phone' => '+90 537 567 8901',
                'address' => 'Beyoğlu, Istanbul',
                'nationality' => 'Turkish',
                'gender' => 'male',
                'birth_date' => Carbon::createFromDate(1987, 2, 18),
                'birth_place_id' => $city->id,
                'license_required' => true,
                'employment_date' => Carbon::createFromDate(2017, 9, 5),
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }
} 