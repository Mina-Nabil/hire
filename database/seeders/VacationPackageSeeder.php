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
                'name' => 'Standard Package',
                'desc' => 'Standard vacation package for all employees',
                'vacation_details' => [
                    [
                        'name' => 'Annual Leave',
                        'type' => 'yearly',
                        'inc_rate_min' => 2.5,
                        'inc_rate_max' => 2.5,
                        'max_balance_min' => 30,
                        'max_balance_max' => 30,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Sick Leave',
                        'type' => 'monthly',
                        'inc_rate_min' => 2.5,
                        'inc_rate_max' => 2.5,
                        'max_balance_min' => 30,
                        'max_balance_max' => 30,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Emergency Leave',
                        'type' => 'daily',
                        'inc_rate_min' => 1,
                        'inc_rate_max' => 1,
                        'max_balance_min' => 5,
                        'max_balance_max' => 5,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                ]
            ],
            [
                'name' => 'Executive Package',
                'desc' => 'Enhanced vacation package for executive positions',
                'vacation_details' => [
                    [
                        'name' => 'Annual Leave',
                        'type' => 'yearly',
                        'inc_rate_min' => 3,
                        'inc_rate_max' => 3,
                        'max_balance_min' => 45,
                        'max_balance_max' => 45,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Sick Leave',
                        'type' => 'monthly',
                        'inc_rate_min' => 3,
                        'inc_rate_max' => 3,
                        'max_balance_min' => 45,
                        'max_balance_max' => 45,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Emergency Leave',
                        'type' => 'daily',
                        'inc_rate_min' => 2,
                        'inc_rate_max' => 2,
                        'max_balance_min' => 10,
                        'max_balance_max' => 10,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Study Leave',
                        'type' => 'daily',
                        'inc_rate_min' => 0,
                        'inc_rate_max' => 0,
                        'max_balance_min' => 30,
                        'max_balance_max' => 30,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                ]
            ],
            [
                'name' => 'Entry Level Package',
                'desc' => 'Basic vacation package for entry level positions',
                'vacation_details' => [
                    [
                        'name' => 'Annual Leave',
                        'type' => 'yearly',
                        'inc_rate_min' => 2,
                        'inc_rate_max' => 2,
                        'max_balance_min' => 21,
                        'max_balance_max' => 21,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Sick Leave',
                        'type' => 'monthly',
                        'inc_rate_min' => 2,
                        'inc_rate_max' => 2,
                        'max_balance_min' => 21,
                        'max_balance_max' => 21,
                        'hour_price_min' => 0,
                        'hour_price_max' => 0,
                    ],
                    [
                        'name' => 'Emergency Leave',
                        'type' => 'daily',
                        'inc_rate_min' => 1,
                        'inc_rate_max' => 1,
                        'max_balance_min' => 3,
                        'max_balance_max' => 3,
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