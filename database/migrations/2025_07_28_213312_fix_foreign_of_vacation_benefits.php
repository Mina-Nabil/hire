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
        Schema::table('vacation_payments', function (Blueprint $table) {
            $table->dropForeign(['vacation_benefit_id']);
            $table->foreign('vacation_benefit_id')->references('id')->on('vacation_benefits')->onDelete('restrict');
        });

        Schema::table('gained_vacations', function (Blueprint $table) {
            $table->dropForeign(['vacation_benefit_id']);
            $table->foreign('vacation_benefit_id')->references('id')->on('vacation_benefits')->onDelete('restrict');
        });

        Schema::table('vacation_benefits', function (Blueprint $table) {
            $table->dropForeign(['vacation_detail_id']);
            $table->foreign('vacation_detail_id')->references('id')->on('vacation_details')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
