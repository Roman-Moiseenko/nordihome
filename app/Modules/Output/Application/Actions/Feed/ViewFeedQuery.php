<?php

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\TagRepositoryInterface;
use App\Modules\Output\Application\DTOs\Feed\FeedViewData;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListCodeData;
use App\Modules\Shared\Application\DTOs\ListNameData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewFeedQuery
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
        private ProductRepositoryInterface $productRepository,
        private TagRepositoryInterface $tagRepository,
    ) {}

    public function execute(int $feedId, UserPermission $userPermission): FeedViewData
    {
        if (!$userPermission->can('output.feed.view'))
            throw new AccessDeniedException();

        $feedEntity = $this->feedRepository->getById($feedId);

        return new FeedViewData(
            id: $feedEntity->id,
            name: $feedEntity->name,
            active: $feedEntity->active ?? false,
            setPreprice: $feedEntity->setPreprice,
            setTitle: $feedEntity->setTitle ?? '',
            setDescription: $feedEntity->setDescription ?? '',
            productsIn: $this->productsToCode($feedEntity->productsIn),
            productsOut: $this->productsToCode($feedEntity->productsOut),
            tagsIn: $this->tagsToName($feedEntity->tagsIn),
            tagsOut: $this->tagsToName($feedEntity->tagsOut),
            categoriesIn: $feedEntity->categoriesIn,
            categoriesOut: $feedEntity->categoriesOut,
            roomsIn: $feedEntity->roomsIn,
            roomsOut: $feedEntity->roomsOut,
            promotionsIn: $feedEntity->promotionsIn,
            promotionsOut: $feedEntity->promotionsOut,
            groupsIn: $feedEntity->groupsIn,
            groupsOut: $feedEntity->groupsOut,
        );
    }

    /**
     * @param int[] $productIds
     * @return ListCodeData[]
     */
    private function productsToCode(array $productIds): array
    {
        if (empty($productIds)) return [];

        return array_map(
            static fn(ProductEntity $product) => new ListCodeData(
                id: (int) $product->id,
                code: (string) $product->code,
            ),
            $this->productRepository->findByIds($productIds),
        );
    }

    /**
     * @param int[] $tagIds
     * @return ListNameData[]
     */
    private function tagsToName(array $tagIds): array
    {
        if (empty($tagIds)) return [];

        return array_map(
            static fn(TagEntity $tag) => new ListNameData(
                id: (int) $tag->id,
                name: $tag->name,
            ),
            $this->tagRepository->findByIds($tagIds),
        );
    }
}
