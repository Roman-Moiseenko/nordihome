<?php

use App\Modules\Accounting\Entity\AccountingDocument;
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
        Schema::create('surplus_documents', function (Blueprint $table) {
            $table->id();
            AccountingDocument::columns($table);
            $table->foreignId('storage_id')->constrained('storages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surplus_documents');
    }
};
