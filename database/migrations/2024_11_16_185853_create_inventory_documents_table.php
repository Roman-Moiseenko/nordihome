<?php

use App\Modules\Accounting\Entity\AccountingDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_documents', function (Blueprint $table) {
            $table->id();
            AccountingDocument::columns($table);
            $table->foreignId('storage_id')->constrained('storages')->onDelete('restrict');
            $table->foreignId('arrival_id')->nullable()->constrained('arrival_documents')->onDelete('restrict');
            $table->foreignId('departure_id')->nullable()->constrained('departure_documents')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_documents');
    }
};
