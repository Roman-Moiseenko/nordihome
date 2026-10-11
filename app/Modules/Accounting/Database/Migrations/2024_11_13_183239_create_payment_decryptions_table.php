<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_decryptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payment_documents')->onDelete('cascade');
            $table->float('amount')->default(0);
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_decryptions');
    }
};
