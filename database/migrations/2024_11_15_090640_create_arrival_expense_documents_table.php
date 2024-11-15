<?php

use App\Modules\Accounting\Entity\AccountingDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arrival_expense_documents', function (Blueprint $table) {
            $table->id();
            AccountingDocument::columns($table);
            $table->boolean('currency')->default(false);
            $table->foreignId('arrival_id')->constrained('arrival_documents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrival_expense_documents');
    }
};
