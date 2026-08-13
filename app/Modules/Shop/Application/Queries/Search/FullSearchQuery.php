<?php

namespace App\Modules\Shop\Application\Queries\Search;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Search\FullSearchData;
use App\Modules\Shop\Application\DTOs\Search\ItemSearchData;
use App\Modules\Shop\Infrastructure\Persistence\Query\CategorySearchQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductSearchQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\RoomSearchQueryRepository;

readonly class FullSearchQuery
{
    public function __construct(
        private ProductSearchQueryRepository $productSearchQueryRepository,
        private ProductIndexQueryRepository  $productIndexQueryRepository,
        private CategorySearchQueryRepository $categorySearchQueryRepository,
        private RoomSearchQueryRepository     $roomSearchQueryRepository,
    )
    {
    }

    public function execute(string $search, ClientContext $clientContext): FullSearchData
    {

        //MAINDO Аналитика

        $allProductIds = $this->productSearchQueryRepository->getProductIdsBySearch($search);

        $productIds = array_slice($allProductIds, 0, FullSearchData::LIMIT_PRODUCTS);
        $productItemsRaw = $this->productIndexQueryRepository->loadProductSearchItems($productIds, $clientContext);
        $productItems = array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $productItemsRaw
        );

        $categoryIds = $this->categorySearchQueryRepository->getCategoryIdsBySearch($search);
        $categoryIds = array_slice($categoryIds, 0, FullSearchData::LIMIT_CATEGORIES);
        $categoryItemsRaw = $this->categorySearchQueryRepository->loadCategorySearchItems($categoryIds, $clientContext);
        $categoryItems = array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $categoryItemsRaw
        );

        $roomIds = $this->roomSearchQueryRepository->getRoomIdsBySearch($search);
        $roomIds = array_slice($roomIds, 0, FullSearchData::LIMIT_ROOMS);
        $roomItemsRaw = $this->roomSearchQueryRepository->loadRoomSearchItems($roomIds, $clientContext);
        $roomItems = array_map(
            fn(array $item) => ItemSearchData::fromArray($item),
            $roomItemsRaw
        );

        return new FullSearchData(
            search: $search,
            products: $productItems,
            categories: $categoryItems,
            rooms: $roomItems,
            recommends: []
        );
    }
}
