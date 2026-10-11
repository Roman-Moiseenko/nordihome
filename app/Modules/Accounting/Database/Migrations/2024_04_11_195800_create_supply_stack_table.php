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
        Schema::create('supply_stack', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('set null');
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->string('comment')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('supply_stack');
    }
};
