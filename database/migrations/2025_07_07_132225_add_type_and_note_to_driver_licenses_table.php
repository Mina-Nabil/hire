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
        Schema::table('driver_licenses', function (Blueprint $table) {
            $table->enum('type', ['Professional Level 1', 'Professional Level 2', 'Professional Level 3', 'Private', 'Agriculture Equipment'])->nullable()->after('expiry_date');
            $table->text('note')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_licenses', function (Blueprint $table) {
            $table->dropColumn(['type', 'note']);
        });
    }
};
