<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Атрибуты-варианты, задающие модификацию (бывший attributes_json).
        Schema::create('modification_attributes', function (Blueprint $table) {
            $table->foreignId('modification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('sort')->default(0);

            $table->primary(['modification_id', 'attribute_id']);
            $table->index(['modification_id', 'sort']);
        });

        // Переносим данные: attributes_json -> modification_attributes.
        $modifications = DB::table('modifications')->select('id', 'attributes_json')->get();
        $inserts = [];

        foreach ($modifications as $modification) {
            $attributeIds = json_decode($modification->attributes_json, true);
            if (!is_array($attributeIds)) {
                continue;
            }

            foreach ($attributeIds as $sort => $attributeId) {
                $inserts[] = [
                    'modification_id' => $modification->id,
                    'attribute_id' => (int) $attributeId,
                    'sort' => (int) $sort,
                ];
            }
        }

        if (!empty($inserts)) {
            DB::table('modification_attributes')->insert($inserts);
        }
    }

    public function down(): void
    {
        // Восстанавливаем attributes_json из modification_attributes.
        $rows = DB::table('modification_attributes')
            ->select('modification_id', 'attribute_id', 'sort')
            ->orderBy('sort')
            ->get();

        $groups = [];
        foreach ($rows as $row) {
            $groups[$row->modification_id][(int) $row->sort] = (int) $row->attribute_id;
        }

        foreach ($groups as $modificationId => $attributeIds) {
            ksort($attributeIds);
            DB::table('modifications')
                ->where('id', $modificationId)
                ->update(['attributes_json' => json_encode(array_values($attributeIds))]);
        }

        Schema::dropIfExists('modification_attributes');
    }
};
