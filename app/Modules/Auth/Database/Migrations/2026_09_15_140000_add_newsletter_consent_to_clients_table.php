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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('newsletter_consented')->default(false);
            $table->timestamp('newsletter_consented_at')->nullable();
            $table->string('newsletter_consent_text_version')->nullable();
            $table->string('newsletter_action_identifier')->nullable();
            $table->string('newsletter_source')->nullable();
            $table->boolean('newsletter_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('newsletter_consented');
            $table->dropColumn('newsletter_consented_at');
            $table->dropColumn('newsletter_consent_text_version');
            $table->dropColumn('newsletter_action_identifier');
            $table->dropColumn('newsletter_source');
            $table->dropColumn('newsletter_active');
        });
    }
};
