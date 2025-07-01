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
            $table->decimal('after_tax_salary', 10, 2)->nullable();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_employee_insurance', 10, 2)->nullable();
            $table->decimal('total_employer_insurance', 10, 2)->nullable();
            $table->decimal('total_employee_medical', 10, 2)->nullable();
            $table->decimal('total_penalties_amount', 10, 2)->nullable();
            $table->decimal('total_overtime_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_employees', function (Blueprint $table) {
            $table->dropColumn('after_tax_salary');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('total_after_tax_amount');
        });
    }
};
