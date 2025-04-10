<?php

use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
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
