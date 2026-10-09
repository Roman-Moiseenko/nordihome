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
        // Значения атрибутов конкретного товара внутри модификации (бывший values_json).
        Schema::create('modification_product_values', function (Blueprint $table) {
            $table->foreignId('modification_product_id')
                ->constrained('modifications_products')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->restrictOnDelete();
            $table->foreignId('variant_id')->constrained('attribute_variants')->restrictOnDelete();

            $table->primary(['modification_product_id', 'attribute_id']);
            $table->index(['attribute_id', 'variant_id']); // фильтрация "все товары с цветом=белый"
        });

        // Переносим данные: values_json -> modification_product_values.
        $rows = DB::table('modifications_products')->select('id', 'values_json')->get();
        $inserts = [];

        foreach ($rows as $row) {
            $values = json_decode($row->values_json, true);
            if (!is_array($values)) {
                continue;
            }

            foreach ($values as $attributeId => $variantId) {
                $inserts[] = [
                    'modification_product_id' => $row->id,
                    'attribute_id' => (int) $attributeId,
                    'variant_id' => (int) $variantId,
                ];
            }
        }

        if (!empty($inserts)) {
            DB::table('modification_product_values')->insert($inserts);
        }

        // Убираем values_json и добавляем уникальность пары (модификация, товар).
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->dropColumn('values_json');
            $table->unique(['modification_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modifications_products', function (Blueprint $table) {
            $table->dropUnique(['modification_id', 'product_id']);
            $table->json('values_json')->nullable();
        });

        // Восстанавливаем values_json из modification_product_values.
        $rows = DB::table('modification_product_values')
            ->select('modification_product_id', 'attribute_id', 'variant_id')
            ->get();

        $groups = [];
        foreach ($rows as $row) {
            $groups[$row->modification_product_id][$row->attribute_id] = $row->variant_id;
        }

        foreach ($groups as $modificationProductId => $values) {
            DB::table('modifications_products')
                ->where('id', $modificationProductId)
                ->update(['values_json' => json_encode($values)]);
        }

        Schema::dropIfExists('modification_product_values');
    }
};
