<?php

use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Interviews\Interview;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update applications table status enum
        $applicationStatuses = "'" . implode("', '", Application::APPLICATION_STATUSES) . "'";
        DB::statement("ALTER TABLE applications MODIFY COLUMN status ENUM($applicationStatuses) NOT NULL DEFAULT 'pending'");

        // Update interviews table status enum
        $interviewStatuses = "'" . implode("', '", Interview::INTERVIEW_STATUSES) . "'";
        DB::statement("ALTER TABLE interviews MODIFY COLUMN status ENUM($interviewStatuses) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};