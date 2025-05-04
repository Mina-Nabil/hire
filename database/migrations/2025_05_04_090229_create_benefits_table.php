<?php

use App\Models\Benefits\AppliedVacation;
use App\Models\Benefits\BaseBenefit;
use App\Models\Benefits\BenefitPackage;
use App\Models\Benefits\BenefitPayment;
use App\Models\Benefits\Payroll;
use App\Models\Benefits\VacationBenefit;
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
        Schema::create('benefit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //S1
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BenefitPackage::class)->constrained()->cascadeOnDelete();
            $table->string('name'); //monthly salary - medical insurance
            $table->enum('type', BaseBenefit::TYPE_LIST);
            $table->float('amount_min'); //5000
            $table->float('amount_max'); //10000
            $table->timestamps();
        });

        Schema::create('vacation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BenefitPackage::class)->constrained()->cascadeOnDelete();
            $table->string('name'); //annual vacation or sick leave or ezn
            $table->float('daily_inc_rate')->default(0); //21 / 12
            $table->float('monthly_inc_rate')->default(0); //21 / 12
            $table->float('yearly_inc_rate')->default(0); //21
            $table->float('max_balance'); //42 - max hours balance 
            $table->float('hour_price')->default(0); //100
            $table->timestamps();
        });

        Schema::table('employee_info', function (Blueprint $table) {
            $table->foreignIdFor(BenefitPackage::class)->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('base_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BenefitPackage::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->float('amount');
            $table->enum('type', BaseBenefit::TYPE_LIST);
            $table->date('start_date');
            $table->date('end_date')->nullable(); //active benefit
            $table->timestamps();
        });

        Schema::create('vacation_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BenefitPackage::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->float('balance');
            $table->date('start_date');
            $table->date('end_date')->nullable(); //active benefit
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users')->cascadeOnDelete();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->float('total_paid');
            $table->float('total_vacation_days');
            $table->float('total_vacation_amount');
            $table->unsignedInteger('total_employees');
            $table->timestamps();
        });

        Schema::create('payroll_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Payroll::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->float('paid');
            $table->float('vacation_days');
            $table->float('vacation_amount');
            $table->float('base_amount');
            $table->timestamps();
        });

        Schema::create('benefit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(BaseBenefit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->enum('status', BenefitPayment::STATUS_LIST);
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('vacation_payments', function (Blueprint $table) {
            //deduct from vacation balance and give money to employee
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(VacationBenefit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->float('new_balance');
            $table->enum('status', BenefitPayment::STATUS_LIST);
            $table->timestamps();
        });

        Schema::create('extra_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->float('amount');
            $table->date('due_date');
            $table->enum('status', BenefitPayment::STATUS_LIST);
            $table->nullableMorphs('payable');
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('applied_vacation', function (Blueprint $table) {
            //remove from vacation balance
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(VacationBenefit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->cascadeOnDelete();
            $table->enum('status', AppliedVacation::STATUS_LIST)->default(AppliedVacation::STATUS_PENDING);
            $table->float('days');
            $table->float('new_balance');
            $table->timestamps();
        });

        Schema::create('gained_vacation', function (Blueprint $table) {
            //add to vacation balance
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(VacationBenefit::class)->constrained()->cascadeOnDelete();
            $table->float('days');
            $table->float('new_balance');
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefits');
        Schema::dropIfExists('benefit_packages');
        Schema::dropIfExists('package_details');
        Schema::dropIfExists('vacation_details');
        Schema::dropIfExists('base_benefits');
        Schema::dropIfExists('vacation_benefits');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('payroll_employees');
        Schema::dropIfExists('benefit_payments');
        Schema::dropIfExists('vacation_payments');
        Schema::dropIfExists('extra_payments');
        Schema::dropIfExists('applied_vacation');
        Schema::dropIfExists('gained_vacation');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('purchases');
    }
};
