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
        Schema::drop('refunds');

        Schema::rename('payment_orders', 'order_additions');

        Schema::table('order_additions', function (Blueprint $table) {
            $table->dropColumn('paid_at');
            $table->dropColumn('class');
            $table->dropColumn('document');
            $table->dropColumn('meta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_additions', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable();
            $table->string('class');
            $table->string('document')->default('');
            $table->json('meta');
        });

        Schema::rename('order_additions', 'payment_orders');

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payment_orders')->onDelete('cascade');
            $table->float('amount');
            $table->string('comment')->default('');
            $table->timestamp('created_at');
        });
    }
};
