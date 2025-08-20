<?php

use App\Models\Recruitment\Interviews\Interview;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nextSteps = "'" . implode("', '", Interview::NEXT_STEPS) . "'";
        
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('next_step')->nullable()->after('status');
        });

        DB::statement("ALTER TABLE interviews MODIFY COLUMN next_step ENUM($nextSteps) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('next_step');
        });
    }
};