<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_documents', function (Blueprint $table) {
            $table->id();
            $table->string('number')->default('');
            $table->boolean('completed')->default(false);
            $table->float('amount', 10,2, true);
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('set null');
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('restrict');
            $table->foreignId('trader_id')->nullable()->constrained('traders')->onDelete('restrict');
            $table->foreignId('staff_id')->constrained('admins')->onDelete('restrict');
            $table->text('comment');
            $table->string('account')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_documents');
    }
};
