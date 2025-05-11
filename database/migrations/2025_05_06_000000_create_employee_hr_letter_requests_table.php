<?php

use App\Models\Personel\Docs\EmployeeHrLetterRequest;
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
        Schema::create('employee_hr_letter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('purpose');
            $table->text('employee_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->enum('status', EmployeeHrLetterRequest::STATUS_LIST)->default(EmployeeHrLetterRequest::STATUS_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_hr_letter_requests');
    }
}; 