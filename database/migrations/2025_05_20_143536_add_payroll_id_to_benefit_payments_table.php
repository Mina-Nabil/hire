<?php

use App\Models\Benefits\Payrolls\Payroll;
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
        Schema::table('benefit_payments', function (Blueprint $table) {
            // Check if the column exists (it might already be defined in the original migration)
            if (!Schema::hasColumn('benefit_payments', 'payroll_id')) {
                $table->foreignIdFor(Payroll::class)->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benefit_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payroll_id');
        });
    }
};
