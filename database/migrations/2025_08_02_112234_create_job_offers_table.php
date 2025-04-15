<?php

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\JobOffers\JobOffer;
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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Application::class)->constrained()->cascadeOnDelete();
            $table->decimal('offered_salary', 10, 2);
            $table->date('proposed_start_date');
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->date('expiry_date');
            $table->date('offer_date')->nullable();
            $table->date('response_date')->nullable();
            $table->text('benefits')->nullable();
            $table->text('notes')->nullable();
            $table->text('response_notes')->nullable();
            $table->enum('status', JobOffer::JOB_OFFER_STATUSES)->default(JobOffer::STATUS_DRAFT);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
}; 