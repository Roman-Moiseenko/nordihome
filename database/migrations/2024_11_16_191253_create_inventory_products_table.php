<?php

use App\Modules\Accounting\Entity\AccountingProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            AccountingProduct::columns($table);
            $table->integer('formal')->default(0);
            $table->float('cost', 10, 2, false);
            $table->foreignId('inventory_id')->constrained('inventory_documents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
