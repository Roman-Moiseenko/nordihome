<?php

use App\Modules\Accounting\Entity\AccountingDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refund_documents', function (Blueprint $table) {
            $table->id();
            AccountingDocument::columns($table);

            $table->foreignId('supply_id')->nullable()->constrained('supply_documents')->onDelete('cascade');
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('cascade');
            $table->foreignId('storage_id')->nullable()->constrained('storages')->onDelete('cascade');
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_documents');
    }
};
