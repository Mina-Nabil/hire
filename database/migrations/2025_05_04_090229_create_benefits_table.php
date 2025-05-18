<?php

use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\SalaryGrade;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Configurations\VacationPackage;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Benefits\Configurations\WorkingDay;
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
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //S1
            $table->text('desc')->nullable();
            $table->float('gross_min'); //5000
            $table->float('gross_max'); //10000
            $table->timestamps();
        });

        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SalaryGrade::class)->constrained()->cascadeOnDelete();
            $table->enum('receiver', PackageDetail::RECEIVER_LIST); //monthly salary - medical insurance
            $table->string('name')->nullable(); //monthly salary - medical insurance
            $table->enum('type', BaseBenefit::TYPE_LIST);
            $table->float('amount_min'); //5000
            $table->float('amount_max'); //10000
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('vacation_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //S1
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('vacation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(VacationPackage::class)->constrained()->cascadeOnDelete();
            $table->string('name'); //annual vacation or sick leave or ezn
            $table->enum('type', VacationDetail::TYPE_LIST);
            $table->float('inc_rate_min'); //min 2 hours per type
            $table->float('inc_rate_max'); //max 12 hours per type
            $table->float('hour_price_min'); //100
            $table->float('hour_price_max'); //150
            $table->float('max_balance_min'); //42 - max hours balance 
            $table->float('max_balance_max'); //56 - max hours balance 
            $table->timestamps();
        });

        Schema::create('benefit_configurations', function (Blueprint $table) {
            //main benefits configuration for an employee
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SalaryGrade::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(VacationPackage::class)->nullable()->constrained()->nullOnDelete();
            $table->enum('attendace_calculation', BenefitConfiguration::ATTENDANCE_CALCULATION_LIST);
            $table->foreignIdFor(Employee::class, 'manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->boolean('is_automatic_overtime')->default(false);
            $table->boolean('is_require_attendance_approval')->default(false);
            $table->float('gross_salary')->nullable();
            $table->float('insurance_amount')->nullable();
            $table->float('daily_working_hours')->nullable();
            $table->float('overtime_rate')->default(1);
            $table->time('working_day_start_min')->nullable();
            $table->time('working_day_start_max')->nullable();
            $table->time('working_day_end_min')->nullable();
            $table->time('working_day_end_max')->nullable();
            $table->timestamps();
        });
        
        Schema::table('positions', function (Blueprint $table) {
            $table->foreignIdFor(SalaryGrade::class, 'salary_grade_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('working_days', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->enum('type', WorkingDay::DAYS_LIST);
        });

        Schema::create('base_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();

            //either package detail from a package or a custom one
            $table->foreignIdFor(PackageDetail::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->enum('receiver', PackageDetail::RECEIVER_LIST);

            //benefit calculation details
            $table->float('amount');
            $table->enum('type', BaseBenefit::TYPE_LIST);

            $table->boolean('is_hidden')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable(); //active benefit
            $table->timestamps();
        });

        Schema::create('vacation_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();

            //either vacation detail from a package or a custom one
            $table->foreignIdFor(VacationDetail::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable(); //custom name

            //vacation calculation details
            $table->enum('type', VacationDetail::TYPE_LIST);
            $table->float('inc_rate');
            $table->float('hour_price'); //100

            //starting and maximum balance
            $table->float('max_balance'); //in hours
            $table->float('current_balance'); //in hours

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
            $table->float('vacation_days'); //hours
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
            $table->foreignIdFor(User::class, 'creator_id')->constrained('users')->restrictOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->float('amount');
            $table->date('due_date');
            $table->enum('status', BenefitPayment::STATUS_LIST);
            $table->nullableMorphs('payable');
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        Schema::create('applied_vacations', function (Blueprint $table) {
            //remove from vacation balance
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(VacationBenefit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Payroll::class)->nullable()->constrained()->cascadeOnDelete();
            $table->enum('status', AppliedVacation::STATUS_LIST)->default(AppliedVacation::STATUS_PENDING);
            $table->unsignedInteger('hours');
            $table->float('new_balance');
            $table->timestamps();
        });

        Schema::create('vacation_days', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AppliedVacation::class)->constrained()->cascadeOnDelete();
            $table->date('vacation_date');
            $table->float('hours');
        });

        Schema::create('gained_vacations', function (Blueprint $table) {
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
        Schema::dropIfExists('salary_grades');
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
