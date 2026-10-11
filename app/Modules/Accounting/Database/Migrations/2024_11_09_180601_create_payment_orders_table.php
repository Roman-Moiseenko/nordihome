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
            $table->foreignId('staff_id')->constrained('admins')->onDelete('restrict');
            $table->text('comment');
            $table->timestamps();
            $table->boolean('manual')->default(false);
            $table->string('bank_purpose')->default('');
            $table->string('bank_number')->default('');
            $table->timestamp('bank_date')->nullable();

            $table->foreignId('recipient_id')->constrained('organizations')->onDelete('restrict');
            $table->foreignId('payer_id')->constrained('organizations')->onDelete('restrict');
            $table->string('recipient_account');
            $table->string('payer_account');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_documents');
    }
};
