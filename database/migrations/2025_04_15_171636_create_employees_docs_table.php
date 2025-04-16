<?php

use App\Models\Base\Bank;
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

        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('contract_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('work_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->timestamps();
        });

        Schema::create('birth_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->enum('type', ['copy', 'verified_copy', 'original']);
            $table->timestamps();
        });

        Schema::create('army_service_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->enum('type', ['copy', 'verified_copy', 'original']);
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['final_exemption', 'temporary_exemption', 'exemption_completed']);
            $table->timestamps();
        });

        Schema::create('employee_s1_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->string('job_name');
            $table->string('s1_number');
            $table->double('s1_amount', 10, 2);
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('employee_s2_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->double('s2_amount', 10, 2);
            $table->unsignedInteger('year');
            $table->timestamps();
        });

        Schema::create('employee_s6_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('police_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->timestamps();
        });

        Schema::create('job_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->unsignedInteger('registration_days');
            $table->date('registration_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path')->nullable();
            $table->enum('status', ['examination', 'issuing', 'covered', 'external_cover']);
            $table->string('insurance_number')->nullable();
            $table->string('medical_card_code')->nullable();
            $table->date('medical_card_start')->nullable();
            $table->date('medical_card_expiry')->nullable();
            $table->boolean('is_doc_111')->default(false);
            $table->date('doc_111_followup')->nullable();
            $table->timestamps();
        });

        Schema::create('external_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('id_number');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('driver_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bank::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('account_number');
            $table->string('bank_employee_code');
            $table->string('old_bank_code')->nullable();
            $table->string('file_path');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('police_records');
    }
};
