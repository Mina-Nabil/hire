<?php

use App\Models\Benefits\Vacations\VacationBenefit;
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
        Schema::table('applied_vacations', function (Blueprint $table) {
            $table->foreignIdFor(VacationBenefit::class)->nullable()->change();
            $table->boolean('is_mission')->default(false);
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
