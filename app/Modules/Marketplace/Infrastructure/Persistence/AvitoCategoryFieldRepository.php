<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCategoryField;
use Illuminate\Support\Facades\DB;

class AvitoCategoryFieldRepository implements AvitoCategoryFieldRepositoryInterface
{
    public function countFieldsByCategoryIds(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        return AvitoCategoryField::whereIn('avito_category_id', $categoryIds)
            ->selectRaw('avito_category_id, COUNT(*) as count')
            ->groupBy('avito_category_id')
            ->pluck('count', 'avito_category_id')
            ->map(fn($value) => (int) $value)
            ->toArray();
    }

    public function syncForCategory(int $avitoCategoryId, array $fields): void
    {
        DB::transaction(function () use ($avitoCategoryId, $fields) {
            AvitoCategoryField::where('avito_category_id', $avitoCategoryId)->delete();

            foreach ($fields as $fieldData) {
                $field = new AvitoCategoryField();
                $field->avito_category_id = $avitoCategoryId;
                $field->tag = $fieldData['tag'];
                $field->name = $fieldData['name'];
                $field->type = $fieldData['type'];
                $field->is_required = $fieldData['is_required'] ?? false;
                $field->allowed_values = $fieldData['allowed_values'] ?? null;
                $field->depends_on = $fieldData['depends_on'] ?? null;
                $field->save();
            }
        });
    }
}
