<?php

use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Vacancies\Vacancy;
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
            $table->foreignIdFor(Area::class);
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
            $table->enum('military_status', ['exempted', 'drafted', 'completed'])->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('image_url')->nullable();
            $table->string('cv_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->dateTime('signature_date')->nullable();
            $table->timestamps();
        });

        Schema::create('applicant_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->string('school_name');
            $table->string('degree');
            $table->string('field_of_study');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
        
        Schema::create('applicant_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->string('name');
            $table->string('sponsor');
            $table->string('duration');
            $table->date('start_date');
            $table->timestamps();
        });

        Schema::create('applicant_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
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
            $table->foreignIdFor(Applicant::class);
            $table->string('language');
            $table->enum('speaking_level', ['Basic', 'Good', 'Very Good', 'Fluent'])->nullable();
            $table->enum('writing_level', ['Basic', 'Good', 'Very Good', 'Fluent'])->nullable();
            $table->enum('reading_level', ['Basic', 'Good', 'Very Good', 'Fluent'])->nullable();
        });

        Schema::create('applicant_references', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('relationship')->nullable();
        });

        Schema::create('applicant_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->string('skill');
            $table->enum('level', ['Basic', 'Good', 'Very Good', 'Excellent'])->nullable();
        });

        Schema::create('applicant_health', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->boolean('has_health_issues')->default(false);
            $table->text('health_issues')->nullable();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class);
            $table->foreignIdFor(Vacancy::class);
            $table->string('cover_letter')->nullable();
            $table->enum('status', ['pending', 'shortlisted', 'interview', 'hired', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('application_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class);
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
