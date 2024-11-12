<?php

use App\Modules\Accounting\Entity\AccountingProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refund_products', function (Blueprint $table) {
            $table->id();
            AccountingProduct::columns($table);
            $table->float('cost_currency')->default(0);
            $table->foreignId('refund_id')->constrained('refund_documents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_products');
    }
};
