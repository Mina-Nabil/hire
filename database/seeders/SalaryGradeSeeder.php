<?php

namespace Database\Seeders;

use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Configurations\VacationPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalaryGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            [
                'name' => 'Grade A',
                'desc' => 'Entry Level Positions',
                'gross_min' => 3000,
                'gross_max' => 5000,
                'package_details' => [
                    [
                        'name' => 'Basic Salary',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 2500,
                        'amount_max' => 4000,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Housing Allowance',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 10,
                        'amount_max' => 15,
                        'is_hidden' => false,
                    ],
                ]
            ],
            [
                'name' => 'Grade B',
                'desc' => 'Mid Level Positions',
                'gross_min' => 5000,
                'gross_max' => 8000,
                'package_details' => [
                    [
                        'name' => 'Basic Salary',
                        'receiver' => 'employee',
                        'type' => 'quarterly',
                        'amount_min' => 4000,
                        'amount_max' => 6500,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Housing Allowance',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 15,
                        'amount_max' => 20,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Transportation Allowance',
                        'receiver' => 'employee',
                        'type' => 'daily',
                        'amount_min' => 500,
                        'amount_max' => 800,
                        'is_hidden' => false,
                    ],
                ]
            ],
            [
                'name' => 'Grade C',
                'desc' => 'Senior Level Positions',
                'gross_min' => 8000,
                'gross_max' => 12000,
                'package_details' => [
                    [
                        'name' => 'Basic Salary',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 6500,
                        'amount_max' => 10000,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Housing Allowance',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 20,
                        'amount_max' => 25,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Transportation Allowance',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 800,
                        'amount_max' => 1200,
                        'is_hidden' => false,
                    ],
                    [
                        'name' => 'Education Allowance',
                        'receiver' => 'employee',
                        'type' => 'monthly',
                        'amount_min' => 1000,
                        'amount_max' => 2000,
                        'is_hidden' => false,
                    ],
                ]
            ],
        ];

        foreach ($grades as $grade) {
            $packageDetails = $grade['package_details'];
            unset($grade['package_details']);
            
            SalaryGrade::createSalaryGrade(
                $grade['name'],
                $grade['gross_min'],
                $grade['gross_max'],
                $packageDetails,
                $grade['desc']
            );
        }

        VacationPackage::createVacationPackage(
            '21 Days Vacation',
            'Default 21 days vacation package',
            [
                [
                    'name' => 'Annual Leave',
                    'type' => 'yearly',
                    'inc_rate_min' => 21,
                    'inc_rate_max' => 21,
                    'max_balance_min' => 42,
                    'max_balance_max' => 42,
                    'hour_price_min' => 0,
                    'hour_price_max' => 0,
                ],
                [
                    'name' => 'Sick Leave',
                    'type' => 'yearly',
                    'inc_rate_min' => 4,
                    'inc_rate_max' => 4,
                    'max_balance_min' => 4,
                    'max_balance_max' => 4,
                    'hour_price_min' => 0,
                    'hour_price_max' => 0,
                ],
            ]
        );

        // Entry Level Package
        SalaryGrade::createSalaryGrade(
            'Entry Level',
            2000,
            3000,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 2000,
                    'amount_max' => 3000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 200,
                    'amount_max' => 300,
                    'is_hidden' => false,
                ],
            ],
            'Basic package for entry-level positions',
        );

        // Junior Level Package
        SalaryGrade::createSalaryGrade(
            'Junior Level',
            3000,
            4500,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 3000,
                    'amount_max' => 4500,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 300,
                    'amount_max' => 400,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Housing Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 10,
                    'amount_max' => 15,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Social Insurance',
                    'receiver' => 'other',
                    'type' => 'monthly',
                    'amount_min' => 11,
                    'amount_max' => 11,
                    'is_hidden' => false,
                ],
            ],
            'Package for junior positions with 2-3 years experience',
        );

        // Mid Level Package
        SalaryGrade::createSalaryGrade(
            'Mid Level',
            4500,
            7000,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 4500,
                    'amount_max' => 7000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'other',
                    'type' => 'monthly',
                    'amount_min' => 400,
                    'amount_max' => 500,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Housing Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 15,
                    'amount_max' => 20,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Performance Bonus',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 5,
                    'amount_max' => 10,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Social Insurance',
                    'receiver' => 'medical',
                    'type' => 'monthly',
                    'amount_min' => 11,
                    'amount_max' => 11,
                    'is_hidden' => false,
                ],
            ],
            'Package for mid-level positions with 4-6 years experience',
        );

        // Senior Level Package
        SalaryGrade::createSalaryGrade(
            'Senior Level',
            7000,
            10000,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 7000,
                    'amount_max' => 10000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 500,
                    'amount_max' => 600,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Housing Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 20,
                    'amount_max' => 25,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Performance Bonus',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 10,
                    'amount_max' => 15,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Social Insurance',
                    'receiver' => 'other',
                    'type' => 'monthly',
                    'amount_min' => 11,
                    'amount_max' => 11,
                    'is_hidden' => false,
                ],
            ],
            'Package for senior positions with 7-10 years experience',
        );

        // Manager Level Package
        SalaryGrade::createSalaryGrade(
            'Manager Level',
            10000,
            15000,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 10000,
                    'amount_max' => 15000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 600,
                    'amount_max' => 800,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Housing Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 25,
                    'amount_max' => 30,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Performance Bonus',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 15,
                    'amount_max' => 20,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Social Insurance',
                    'receiver' => 'medical',
                    'type' => 'monthly',
                    'amount_min' => 11,
                    'amount_max' => 11,
                    'is_hidden' => false,
                ],
            ],
            'Package for managerial positions with 10+ years experience',
        );

        // Executive Level Package
        SalaryGrade::createSalaryGrade(
            'Executive Level',
            15000,
            25000,
            [
                [
                    'name' => 'Basic Salary',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 15000,
                    'amount_max' => 25000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Transportation Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 800,
                    'amount_max' => 1000,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Housing Allowance',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 30,
                    'amount_max' => 35,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Performance Bonus',
                    'receiver' => 'employee',
                    'type' => 'monthly',
                    'amount_min' => 20,
                    'amount_max' => 25,
                    'is_hidden' => false,
                ],
                [
                    'name' => 'Social Insurance',
                    'receiver' => 'other',
                    'type' => 'monthly',
                    'amount_min' => 11,
                    'amount_max' => 11,
                    'is_hidden' => false,
                ],
            ],
            'Package for executive positions with 15+ years experience',
        );
    }
}
