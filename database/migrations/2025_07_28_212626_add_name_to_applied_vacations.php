<?php

use App\Models\Benefits\Payrolls\AppliedVacation;
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
            $table->string('name')->nullable();
        });
        foreach(AppliedVacation::all() as $appliedVacation){
            $appliedVacation->name = $appliedVacation->vacationBenefit->name;
            $appliedVacation->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applied_vacations', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
