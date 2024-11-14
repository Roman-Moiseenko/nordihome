<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::table('refund_documents', function (Blueprint $table) {
              $table->dropForeign(['supply_id']);
              $table->dropForeign(['arrival_id']);
          });
          Schema::table('refund_documents', function (Blueprint $table) {
              $table->dropColumn('supply_id');
              $table->dropColumn('arrival_id');
          });
        Schema::table('refund_documents', function (Blueprint $table) {
            $table->foreignId('arrival_id')->constrained('arrival_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('refund_documents', function (Blueprint $table) {
            $table->dropForeign(['arrival_id']);
        });
        Schema::table('refund_documents', function (Blueprint $table) {
            $table->dropColumn('arrival_id');
        });

        Schema::table('refund_documents', function (Blueprint $table) {
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('cascade');
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('cascade');
        });
    }
};
