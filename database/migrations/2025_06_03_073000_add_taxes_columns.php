<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add tax_amount column to payroll_employees table
        Schema::table('payroll_employees', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->nullable();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('total_tax_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_employees', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('total_tax_amount');
        });
    }
};
