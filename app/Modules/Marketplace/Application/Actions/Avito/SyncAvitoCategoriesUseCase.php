<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Application\Services\Avito\AvitoCategoryApiService;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SyncAvitoCategoriesUseCase
{
    /**
     * Авито обрезает слишком глубокие ветки и возвращает этот плейсхолдер.
     */
    private const string SKIP_MARKER = 'Over 9 levels deep, aborting normalization';

    public function __construct(
        private AvitoCategoryApiService $apiService,
        private AvitoCategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $tree = $this->apiService->fetchCategoryTree();
        $flat = $this->flatten($tree);

        $this->categoryRepository->sync($flat);
    }

    /**
     * @return array<int, array{slug: string, name: string, parent_slug: ?string}>
     */
    private function flatten(array $nodes, ?string $parentSlug = null): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $name = $node['name'] ?? '';
            $slug = $node['slug'] ?? '';

            // Плейсхолдеры слишком глубоких веток не добавляем.
            if ($name === self::SKIP_MARKER || $slug === self::SKIP_MARKER) {
                continue;
            }

            $result[] = [
                'slug' => $slug,
                'name' => $name,
                'parent_slug' => $parentSlug,
            ];

            // Вложенные категории приходят в ключе "nested"; для обрезанных
            // веток это строка-плейсхолдер, а не массив.
            $nested = $node['nested'] ?? [];
            if (is_array($nested) && !empty($nested)) {
                $result = array_merge($result, $this->flatten($nested, $slug));
            }
        }

        return $result;
    }
}
