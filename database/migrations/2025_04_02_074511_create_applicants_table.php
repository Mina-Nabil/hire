<?php

use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\ApplicantSkill;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Applicants\Language;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancySlot;
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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Area::class)->constrained('areas');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('nationality')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('address')->nullable();
            $table->string('social_number')->nullable();
            $table->string('home_phone')->unique();
            $table->date('birth_date')->nullable();
            $table->enum('military_status', Applicant::MILITARY_STATUS)->nullable();
            $table->enum('gender', Applicant::GENDER)->nullable();
            $table->enum('marital_status', Applicant::MARITAL_STATUS)->nullable();
            $table->string('image_url')->nullable();
            $table->string('cv_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->dateTime('signature_date')->nullable();
            $table->timestamps();
        });

        Schema::create('applicant_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants');
            $table->string('school_name');
            $table->string('degree');
            $table->string('field_of_study');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
        
        Schema::create('applicant_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->string('name');
            $table->string('sponsor');
            $table->string('duration');
            $table->date('start_date');
            $table->timestamps();
        });

        Schema::create('applicant_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->date('start_date');
            $table->string('salary')->nullable();
            $table->string('reason_for_leaving')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
        
        Schema::create('applicant_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->string('language');
            $table->enum('speaking_level', Language::PROFICIENCY_LEVELS)->nullable();
            $table->enum('writing_level', Language::PROFICIENCY_LEVELS)->nullable();
            $table->enum('reading_level', Language::PROFICIENCY_LEVELS)->nullable();
        });

        Schema::create('applicant_references', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('relationship')->nullable();
        });

        Schema::create('applicant_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->string('skill');
            $table->enum('level', ApplicantSkill::SKILL_LEVELS)->nullable();
        });

        Schema::create('applicant_health', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->boolean('has_health_issues')->default(false);
            $table->text('health_issues')->nullable();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->constrained('applicants')->cascadeOnDelete();
            $table->foreignIdFor(Vacancy::class)->constrained('vacancies')->cascadeOnDelete();
            $table->string('cover_letter')->nullable();
            $table->enum('status', Application::APPLICATION_STATUSES)->default(Application::STATUS_PENDING);
            $table->timestamps();
        });
        
        Schema::create('application_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class)->constrained('applications')->cascadeOnDelete();
            $table->foreignIdFor(VacancySlot::class)->constrained('vacancy_slots')->cascadeOnDelete();
        });

        Schema::create('application_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class)->constrained('applications')->cascadeOnDelete();
            $table->morphs('answerable');
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
