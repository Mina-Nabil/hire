<?php

use App\Models\Base\Bank;
use App\Models\Base\InsuranceOffice;
use App\Models\Personel\Docs\ArmyServicePaper;
use App\Models\Personel\Docs\BirthCertificate;
use App\Models\Personel\Docs\EmployeeS6Doc;
use App\Models\Personel\Docs\MedicalRecord;
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

        Schema::create('employee_info', function(Blueprint $table){
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();

            $table->foreignIdFor(InsuranceOffice::class)->constrained()->restrictOnDelete();
            $table->string('insurance_number')->nullable();
            $table->double('insurance_amount', 10, 2)->nullable();

            $table->string('academic_qualification')->nullable();
            $table->string('university')->nullable();
            $table->string('graduation_year')->nullable();

            $table->enum('military_status', Applicant::MILITARY_STATUS);
            $table->enum('gender', Applicant::GENDER);
            $table->enum('marital_status', Applicant::MARITAL_STATUS);

            $table->unsignedInteger('children_count')->default(0);
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_address')->nullable();

            $table->foreignIdFor(Employee::class, 'previous_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('work_declarations', function (Blueprint $table) {
            //ka3b 3amal
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('birth_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('type', BirthCertificate::TYPES);
            $table->timestamps();
        });

        Schema::create('army_service_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->enum('type', ArmyServicePaper::TYPES);
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_s1_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->string('s1_number');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_s2_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->double('s2_amount', 10, 2);
            $table->unsignedInteger('year');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_s6_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('s6_number');
            $table->enum('leaving_reason', EmployeeS6Doc::LEAVING_REASONS);
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('police_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->enum('status', MedicalRecord::STATUSES)->default(MedicalRecord::STATUS_NOT_COVERED);

            $table->string('file_path')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->string('medical_card_code')->nullable();
            $table->date('medical_card_start')->nullable();
            $table->date('medical_card_expiry')->nullable();

            $table->enum('status_111', MedicalRecord::STATUS111_STATUSES)->default(MedicalRecord::STATUS111_UNAVAILABLE);
            $table->date('doc_111_followup')->nullable();
            $table->timestamps();
        });

        Schema::create('external_medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('id_number');
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
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date'); //credit card issue date
            $table->date('expiry_date')->nullable(); //credit card expiry date
            $table->foreignIdFor(Bank::class)->constrained()->restrictOnDelete();
            $table->string('account_number');
            $table->string('bank_employee_code');
            $table->string('old_bank_code')->nullable();
            $table->timestamps();
        });

        Schema::create('syndicate_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('skills_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('practice_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->string('file_path');
            $table->date('issue_date');
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
        Schema::dropIfExists('hr_letters');
        Schema::dropIfExists('medical_records');
        Schema::dropIfExists('external_medical_records');
        Schema::dropIfExists('id_cards');
        Schema::dropIfExists('driver_licenses');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('syndicate_cards');
        Schema::dropIfExists('skills_qualifications');
        Schema::dropIfExists('practice_cards');
        Schema::dropIfExists('employee_s1_docs');
        Schema::dropIfExists('employee_s2_docs');
        Schema::dropIfExists('employee_s6_docs');
        Schema::dropIfExists('employee_contracts');
        Schema::dropIfExists('employee_info');
        Schema::dropIfExists('birth_certificates');
        Schema::dropIfExists('army_service_papers');
        Schema::dropIfExists('work_declarations');
        
    }
};
