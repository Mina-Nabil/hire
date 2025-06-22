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
        Schema::create('doc_managers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type')->unique(); // Document type identifier
            $table->string('name'); // Human readable name
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_required')->default(true); // Whether the document is required
            $table->boolean('is_active')->default(true); // Whether the document type is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_managers');
    }
};
