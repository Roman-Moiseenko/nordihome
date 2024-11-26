<?php

use App\Modules\Accounting\Entity\AccountingProduct;
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
        Schema::create('surplus_products', function (Blueprint $table) {
            $table->id();
            AccountingProduct::columns($table);
            $table->decimal('cost', 10, 2);
            $table->foreignId('surplus_id')->constrained('surplus_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surplus_products');
    }
};
