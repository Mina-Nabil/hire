<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayrollIdToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropForeign(['payroll_id']);
            $table->dropColumn('payroll_id');
        });
    }
} 