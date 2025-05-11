<?php

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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users')->restrictOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users')->restrictOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->float('hours');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('public_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('overtimes');
        Schema::dropIfExists('public_holidays');
    }
};
