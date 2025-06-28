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
        Schema::table('vacation_benefits', function (Blueprint $table) {
            $table->boolean('automatic_add_to_balance')->default(false)->after('apply_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacation_benefits', function (Blueprint $table) {
            $table->dropColumn('automatic_add_to_balance');
        });
    }
};
