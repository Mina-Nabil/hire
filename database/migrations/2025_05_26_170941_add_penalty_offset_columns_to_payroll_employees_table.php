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
            // Add penalty offset columns
            $table->decimal('total_penalty_hours', 8, 2)->default(0)->after('penalties_amount');
            $table->decimal('vacation_offset_hours', 8, 2)->default(0)->after('total_penalty_hours');
            $table->decimal('new_vacation_hours', 8, 2)->default(0)->after('vacation_offset_hours');
            $table->decimal('direct_deduction_hours', 8, 2)->default(0)->after('new_vacation_hours');
            $table->decimal('direct_deduction_amount', 10, 2)->default(0)->after('direct_deduction_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_employees', function (Blueprint $table) {
            // Remove penalty offset columns
            $table->dropColumn([
                'total_penalty_hours',
                'vacation_offset_hours',
                'new_vacation_hours',
                'direct_deduction_hours',
                'direct_deduction_amount'
            ]);
        });
    }
};
