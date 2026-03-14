<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('expected_penalty_type', [
                'missing_start_or_end_time',
                'missing_start_and_end_time',
                'late_arrival',
                'early_departure',
                'early_and_late_penalty',
            ])->nullable()->after('penalized_hours');
            $table->decimal('expected_penalty_hours', 8, 2)->nullable()->after('expected_penalty_type');
            $table->decimal('expected_penalty_amount', 10, 2)->nullable()->after('expected_penalty_hours');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['expected_penalty_type', 'expected_penalty_hours', 'expected_penalty_amount']);
        });
    }
};
