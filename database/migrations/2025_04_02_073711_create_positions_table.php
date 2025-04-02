<?php

use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Employee;
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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Department::class)->constrained('departments');
            $table->string('name');
            $table->string('arabic_name');

            $table->text('job_description')->nullable();
            $table->text('arabic_job_description')->nullable();

            $table->text('job_requirements')->nullable();
            $table->text('arabic_job_requirements')->nullable();

            $table->text('job_qualifications')->nullable();
            $table->text('arabic_job_qualifications')->nullable();

            $table->text('job_benefits')->nullable();
            $table->text('arabic_job_benefits')->nullable();

            $table->foreignIdFor(Position::class, 'parent_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
