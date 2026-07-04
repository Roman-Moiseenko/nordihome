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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('type', ['retail', 'purchase', 'minimal', 'wholesale']);
            $table->decimal('amount', 12, 2);           // или int для копеек
            $table->string('currency', 3)->default('RUB');
            $table->timestamp('set_at')->useCurrent();  // дата установки цены
            $table->text('comment')->nullable();

            // Внешний ключ
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            // Индекс для быстрого получения последней цены нужного типа
            $table->index(['product_id', 'type', 'set_at'], 'product_type_set_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
