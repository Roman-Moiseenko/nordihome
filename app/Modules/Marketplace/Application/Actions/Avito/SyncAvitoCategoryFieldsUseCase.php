<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\Services\Avito\AvitoCategoryFieldApiService;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCategory;

class SyncAvitoCategoryFieldsUseCase
{
    public function __construct(
        private readonly AvitoCategoryFieldApiService $apiService,
    ) {}


    /**
     * Синхронизирует поля для всех листовых категорий.
     */
    public function execute(): void
    {
        // Берем только листья, чтобы не тратить лимиты API на родительские узлы
        $leafCategories = AvitoCategory::whereDoesntHave('children')->get();

        foreach ($leafCategories as $category) {
            try {
                $fields = $this->apiService->fetchFields($category->slug);
                $this->saveFields($category, $fields);
            } catch (\Throwable $e) {
                report($e);
                continue;
            }
        }
    }

    private function saveFields(AvitoCategory $category, array $fields): void
    {
        DB::transaction(function () use ($category, $fields) {
            // Удаляем старые поля, чтобы избежать дублей при повторной синхронизации
            $category->fields()->delete();

            foreach ($fields as $fieldData) {
                $category->fields()->create([
                    'tag'            => $fieldData['tag'],
                    'name'           => $fieldData['name'],
                    'type'           => $fieldData['type'],
                    'is_required'    => $fieldData['is_required'] ?? false,
                    'allowed_values' => $fieldData['allowed_values'] ?? null,
                    'depends_on'     => $fieldData['depends_on'] ?? null,
                ]);
            }
        });
    }
}
