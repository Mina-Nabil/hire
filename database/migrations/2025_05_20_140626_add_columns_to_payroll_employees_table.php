<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payroll_employees', function (Blueprint $table) {
            // Basic salary information
            $table->float('gross_salary')->after('base_amount');
            $table->float('insurance_amount')->after('gross_salary');
            $table->float('other_amount')->after('insurance_amount');
            
            // Social insurance details
            $table->float('employee_insurance')->after('other_amount');
            $table->float('employer_insurance')->after('employee_insurance');
            $table->float('total_insurance')->after('employer_insurance');
            
            // Medical insurance details
            $table->float('employee_medical')->after('total_insurance');
            $table->float('total_medical')->after('employee_medical');
            
            // Deductions
            $table->float('employee_deductions')->after('total_medical');
            
            // Penalties
            $table->float('penalties_days')->after('employee_deductions');
            $table->float('penalties_amount')->after('penalties_days');
            $table->float('net_after_penalty')->after('penalties_amount');
            
            // Extra payments
            $table->float('extra_payments')->after('net_after_penalty');
            $table->float('net_after_deductions')->after('extra_payments');
            
            // Base benefits
            $table->float('employee_base_benefits')->after('net_after_deductions');
            $table->float('other_base_benefits')->after('employee_base_benefits');
            
            // Department and position
            $table->string('position')->nullable()->after('other_base_benefits');
            $table->string('department')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_employees', function (Blueprint $table) {
            $table->dropColumn([
                'gross_salary',
                'insurance_amount',
                'other_amount',
                'employee_insurance',
                'employer_insurance',
                'total_insurance',
                'employee_medical',
                'total_medical',
                'employee_deductions',
                'penalties_days',
                'penalties_amount',
                'net_after_penalty',
                'extra_payments',
                'net_after_deductions',
                'employee_base_benefits',
                'other_base_benefits',
                'position',
                'department',
            ]);
        });
    }
};
