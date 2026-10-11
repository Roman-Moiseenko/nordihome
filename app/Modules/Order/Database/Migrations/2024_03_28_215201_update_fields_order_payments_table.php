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
        $payments = \App\Modules\Order\Entity\Order\OrderPayment::orderBy('id')->get();
        foreach ($payments as $payment) {
            $payment->delete();
        }
        Schema::table('order_payments', function (Blueprint $table) {
            $table->integer('staff_id')->nullable();
            $table->dropColumn('created_at');
        });
        Schema::table('order_payments', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn('staff_id');
            $table->dropColumn('updated_at');
        });
    }
};
