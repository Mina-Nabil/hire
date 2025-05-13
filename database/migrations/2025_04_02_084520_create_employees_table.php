<?php

use App\Models\Base\City;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Applicant::class)->nullable()->constrained('applicants')->nullOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('name');
            $table->string('name_ar');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('nationality');
            $table->string('mother_name')->nullable();
            $table->enum('gender', Applicant::GENDER);
            $table->date('birth_date');
            $table->string('image_url')->nullable();
            $table->foreignIdFor(City::class, 'birth_place_id')->constrained('cities');
            $table->boolean('license_required')->default(false);
            $table->date('employment_date');
            $table->date('termination_date')->nullable();

            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class, 'referred_by_id')->nullable()->constrained('employees');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignIdFor(Employee::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
