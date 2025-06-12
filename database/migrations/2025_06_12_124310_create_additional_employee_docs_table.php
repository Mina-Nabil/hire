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
        Schema::create('labour_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->timestamps();
        });

        Schema::create('college_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->timestamps();
        });

        Schema::create('social_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_prints');
        Schema::dropIfExists('college_certificates');
        Schema::dropIfExists('labour_documents');
    }
};
