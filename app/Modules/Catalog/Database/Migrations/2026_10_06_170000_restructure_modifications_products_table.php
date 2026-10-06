<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавляем суррогатный id, признак базового товара и timestamps.
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Переносим признак базового товара: бывший base_product_id -> is_primary = true.
        $baseProductIds = DB::table('modifications')->pluck('base_product_id', 'id');

        foreach ($baseProductIds as $modificationId => $baseProductId) {
            DB::table('modifications_products')
                ->where('modification_id', $modificationId)
                ->where('product_id', $baseProductId)
                ->update(['is_primary' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->dropColumn('is_primary');
            $table->dropTimestamps();
        });

        // MySQL: снимаем PRIMARY KEY и удаляем автоинкрементный id.
        DB::statement('ALTER TABLE modifications_products DROP PRIMARY KEY, DROP COLUMN id');
    }
};
