<?php

use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Payrolls\PenaltyDay;
use App\Models\Personel\Employee;
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
        Schema::create('penalty_days', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->foreignIdFor(Payroll::class)->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('type', PenaltyDay::PENALTY_TYPE_LIST);
            $table->decimal('hours', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_days');
    }
};
