<?php

use App\Models\Benefits\Payrolls\PeriodicExtraPayment;
use App\Models\Personel\Employee;
use App\Models\Users\User;
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
        Schema::create('periodic_extra_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users')->restrictOnDelete();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('desc')->nullable();
            $table->enum('frequency', PeriodicExtraPayment::FREQUENCIES);
            $table->date('start_date');            // anchor: the first payment's due date
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_extra_payments');
    }
};
