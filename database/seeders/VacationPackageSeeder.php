<?php

namespace Database\Seeders;

use App\Models\Benefits\Configurations\VacationPackage;
use Illuminate\Database\Seeder;

class VacationPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => '21 Days Package',
                'desc' => '21 Days vacation package for all employees',
                'vacation_details' => [
                    [
                        'name' => 'Annual Leave',
                        'type' => 'yearly',
                        'inc_rate_min' => 21 * 8,
                        'inc_rate_max' => 21 * 8,
                        'max_balance_min' => 21 * 8,
                        'max_balance_max' => 21 * 8 * 2,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                ]
            ],
            [
                'name' => '30 Days Package',
                'desc' => '30 Days vacation package for all employees',
                'vacation_details' => [
                    [
                        'name' => 'Annual Leave',
                        'type' => 'yearly',
                        'inc_rate_min' => 30 * 8,
                        'inc_rate_max' => 30 * 8,
                        'max_balance_min' => 30 * 8,
                        'max_balance_max' => 30 * 8 * 2,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                ]
            ],
        ];

        foreach ($packages as $package) {
            VacationPackage::createVacationPackage(
                $package['name'],
                $package['desc'],
                $package['vacation_details']
            );
        }
    }
} 