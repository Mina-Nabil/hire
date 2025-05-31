<?php

use App\Models\Attendance\Bus;
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
        Schema::create('buses', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('bus_arrivals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bus::class)->constrained()->restrictOnDelete();
            $table->date('date');
            $table->time('time');
            $table->timestamps();
        });

        Schema::table('benefit_configurations', function (Blueprint $table) {
            $table->foreignIdFor(Bus::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_attendance');
    }
};
