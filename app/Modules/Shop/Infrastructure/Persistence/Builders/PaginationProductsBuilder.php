<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Builders;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Infrastructure\Persistence\Query\AttributeQueryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class PaginationProductsBuilder
{
    public function __construct(
        private AttributeQueryRepository $attributeQueryRepository,
    )
    {
    }
    public function execute(array $filters, $allIds, int $page, int $perPage): LengthAwarePaginator
    {

        if (empty($allIds)) {
            return new LengthAwarePaginator(
                items: collect([]),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $query = Product::whereIn('id', $allIds);

        $this->attributeQueryRepository->applyFilters($query, $filters);

        $this->attributeQueryRepository->applySorting($query, $filters['order'] ?? '');


        // Пагинируем через Eloquent
        $paginator = $query->paginate($perPage, ['id'], 'page', $page);

        // Возвращаем пагинатор только с ID товаров
        $ids = $paginator->getCollection()->pluck('id')->toArray();

        return new LengthAwarePaginator(
            items: collect($ids),
            total: $paginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }
}
