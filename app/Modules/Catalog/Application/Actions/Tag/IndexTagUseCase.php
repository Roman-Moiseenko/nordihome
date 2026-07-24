<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\DTOs\Tag\TagIndexData;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexTagUseCase
{
    public function __construct(
        private TagRepositoryInterface        $tagRepository,
        private TagProductRepositoryInterface $tagProductRepository,
    )
    {
    }

    public function execute(UserPermission $userPermission, int $perPage = 20): LengthAwarePaginator
    {
        if (!$userPermission->can('catalog.product.view')) throw new \DomainException('Доступ запрещён');

        // 1. Получаем пагинированные сущности тегов
        $paginator = $this->tagRepository->paginate($perPage);

        // 2. Собираем массив ID тегов из текущей страницы
        $tagIds = $paginator->getCollection()->map(fn(TagEntity $tag) => $tag->id)->toArray();

        // 3. Получаем счётчики товаров для этих тегов
        $counts = $this->tagProductRepository->countProductsByTagIds($tagIds);

        // 4. Преобразуем сущности в DTO, подставляя счётчик
        $dtos = $paginator->getCollection()->map(
            fn(TagEntity $tag) => TagIndexData::fromEntity($tag, $counts[$tag->id] ?? 0)
        );
        $paginator->setCollection($dtos);

        return $paginator;

    }
}
