<?php

use App\Models\Recruitment\Interviews\Interview;
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
        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('interview_level', Interview::INTERVIEW_LEVELS)->default(Interview::INTERVIEW_LEVEL_FIRST);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('interview_level');
        });
    }
};