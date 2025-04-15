<?php

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\Interviews\InterviewFeedback;
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
            $table->foreignIdFor(Application::class)->constrained('applications')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
            $table->dateTime('date');
            $table->enum('type', Interview::INTERVIEW_TYPES)->default(Interview::TYPE_IN_PERSON);
            $table->string('location')->nullable();
            $table->string('zoom_link')->nullable();
            $table->enum('status', Interview::INTERVIEW_STATUSES)->default(Interview::STATUS_PENDING);
            $table->timestamps();
        });

        Schema::create('interview_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Interview::class)->constrained('interviews')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
        });

        Schema::create('interview_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Interview::class)->constrained('interviews')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
            $table->string('title');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('interview_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Interview::class)->constrained('interviews')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
            $table->enum('result', InterviewFeedback::RESULTS);
            $table->integer('rating');
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('feedback')->nullable();
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
