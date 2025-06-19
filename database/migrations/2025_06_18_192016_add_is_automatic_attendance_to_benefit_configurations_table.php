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
        Schema::table('benefit_configurations', function (Blueprint $table) {
            $table->boolean('is_generate_overtime')->default(false)->after('is_automatic_overtime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benefit_configurations', function (Blueprint $table) {
            $table->dropColumn('is_generate_overtime');
        });
    }
};
