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
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('id_card_url')->nullable()->after('signature_date');
            $table->string('birth_certificate_url')->nullable()->after('id_card_url');
            $table->string('college_certificate_url')->nullable()->after('birth_certificate_url');
            $table->string('army_certificate_url')->nullable()->after('college_certificate_url');
            $table->dropUnique(['email']);
            $table->dropUnique(['phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'id_card_url',
                'birth_certificate_url',
                'college_certificate_url',
                'army_certificate_url'
            ]);
            $table->unique(['email']);
            $table->unique(['phone']);
        });
    }
};
