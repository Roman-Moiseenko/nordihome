<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\Services\Avito\AvitoCategoryApiService;
use App\Modules\Marketplace\Infrastructure\Models\AvitoCategory;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Support\Facades\DB;

readonly class SyncAvitoCategoriesUseCase
{
    public function __construct(
        private AvitoCategoryApiService $apiService,
    ) {}


    public function execute(UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) throw new AccessDeniedException();

        $tree = $this->apiService->fetchCategoryTree();
        $flat = $this->flatten($tree);

        $incomingSlugs = array_column($flat, 'slug');

        DB::transaction(function () use ($flat, $incomingSlugs) {
            // 1. Удаляем категории, которых больше нет на Авито.
            //    Товары получат null через FK nullOnDelete.
            AvitoCategory::whereNotIn('slug', $incomingSlugs)->delete();

            // 2. Upsert по slug. Существующие — обновляем name/parent, id не меняется,
            //    товары сохраняют связь.
            $idBySlug = AvitoCategory::pluck('id', 'slug')->all();

            foreach ($flat as $node) {
                $parentId = $node['parent_slug'] !== null
                    ? ($idBySlug[$node['parent_slug']] ?? null)
                    : null;

                AvitoCategory::updateOrCreate(
                    ['slug' => $node['slug']],
                    ['name' => $node['name'], 'parent_id' => $parentId],
                );
            }

            // 3. Пересобрать NestedSet после массовых изменений parent_id.
            AvitoCategory::fixTree();
        });
    }

    /**
     * @return array<int, array{slug: string, name: string, parent_slug: ?string}>
     */
    private function flatten(array $nodes, ?string $parentSlug = null): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $result[] = [
                'slug'        => $node['slug'],
                'name'        => $node['name'],
                'parent_slug' => $parentSlug,
            ];
            if (!empty($node['children'])) {
                $result = array_merge($result, $this->flatten($node['children'], $node['slug']));
            }
        }
        return $result;
    }
}
