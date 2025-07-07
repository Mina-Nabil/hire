<?php

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
        Schema::table('labour_documents', function (Blueprint $table) {
            $table->enum('type', ['Available', 'Not Available', 'Registered'])->nullable()->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('labour_documents', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
