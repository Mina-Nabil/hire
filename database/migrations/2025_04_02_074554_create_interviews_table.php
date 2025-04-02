<?php

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Interviews\Interview;
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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class);
            $table->foreignIdFor(User::class);
            $table->dateTime('date');
            $table->string('location');
            $table->string('zoom_link')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('interview_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Interview::class);
            $table->foreignIdFor(User::class);
        });

        Schema::create('interview_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Interview::class);
            $table->foreignIdFor(User::class);
            $table->string('title');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_notes');
        Schema::dropIfExists('interview_users');
        Schema::dropIfExists('interviews');
    }
};
