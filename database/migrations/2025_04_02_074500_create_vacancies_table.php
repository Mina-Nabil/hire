<?php

use App\Models\Hierarchy\Position;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Recruitment\Vacancies\Vacancy;
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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'assigned_to')->constrained('users');
            $table->foreignIdFor(Position::class)->constrained('positions');
            $table->enum('type', ['full_time', 'part_time', 'temporary']);
            $table->enum('status', ['open', 'closed']);
            $table->date('closing_date')->nullable();

            $table->text('job_responsibilities')->nullable();
            $table->text('arabic_job_responsibilities')->nullable();

            $table->text('job_qualifications')->nullable();
            $table->text('arabic_job_qualifications')->nullable();

            $table->text('job_benefits')->nullable();
            $table->text('arabic_job_benefits')->nullable();
            
            $table->text('job_salary')->nullable();
            $table->timestamps();
        });

        Schema::create('base_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('arabic_question')->nullable();
            $table->enum('type', BaseQuestion::TYPES);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('vacancy_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vacancy::class)->constrained('vacancies');
            $table->string('question');
            $table->string('arabic_question')->nullable();
            $table->enum('type', BaseQuestion::TYPES);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->timestamps();
        });
        
        Schema::create('vacancy_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vacancy::class)->constrained('vacancies');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancy_slots');
        Schema::dropIfExists('vacancy_questions');
        Schema::dropIfExists('base_questions');
        Schema::dropIfExists('vacancies');
    }
};
