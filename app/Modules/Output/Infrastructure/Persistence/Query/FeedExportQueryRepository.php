<?php

declare(strict_types=1);

namespace App\Modules\Output\Infrastructure\Persistence\Query;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Репозиторий чтения данных для выгрузки фида.
 *
 * Собирает товары из всех связанных с фидом сущностей (товары, теги,
 * категории, комнаты, группы, акции) с минимизацией N+1 (eager loading
 * галереи) и кеширует результат на сутки.
 */
class FeedExportQueryRepository
{
    public function getCachedExport(FeedEntity $feed): array
    {
        $version = (int) Cache::get(CacheInvalidationRegistry::FEED_VERSION, 0);

        $key = str_replace(
            ['{id}', '{version}'],
            [(string) $feed->id, (string) $version],
            CacheInvalidationRegistry::FEED_EXPORT,
        );

        return Cache::remember(
            $key,
            now()->addDay(),
            fn () => $this->compute($feed),
        );
    }

    /**
     * @return array{info: array, products: array, categories: array}
     */
    private function compute(FeedEntity $feed): array
    {
        $info = $this->getInfo($feed);

        $ins = $this->getProductIds($feed, true);
        $outs = $this->getProductIds($feed, false);
        $ids = array_values(array_diff($ins, $outs));

        $products = $this->productsToArray($ids);

        $categoryIds = array_values(array_unique(array_filter(array_map(
            static fn (array $product) => $product['category'],
            $products,
        ))));

        return [
            'info' => $info,
            'products' => $products,
            'categories' => $this->categoriesToArray($categoryIds),
        ];
    }

    /**
     * @return int[]
     */
    private function getProductIds(FeedEntity $feed, bool $in): array
    {
        $products = $in ? $feed->productsIn : $feed->productsOut;
        $tags = $in ? $feed->tagsIn : $feed->tagsOut;
        $categories = $in ? $feed->categoriesIn : $feed->categoriesOut;
        $rooms = $in ? $feed->roomsIn : $feed->roomsOut;
        $groups = $in ? $feed->groupsIn : $feed->groupsOut;
        $promotions = $in ? $feed->promotionsIn : $feed->promotionsOut;

        $query = Product::query()->whereRaw('1 = 0');

        if ($products) {
            $query->orWhereIn('id', $products);
        }
        if ($tags) {
            $query->orWhereHas('tags', fn ($q) => $q->whereIn('id', $tags));
        }
        if ($categories) {
            $query->orWhereHas('categories', fn ($q) => $q->whereIn('id', $categories));
            $query->orWhereIn('main_category_id', $categories);
        }
        if ($rooms) {
            $query->orWhereHas('rooms', fn ($q) => $q->whereIn('id', $rooms));
        }
        if ($groups) {
            $query->orWhereHas('groups', fn ($q) => $q->whereIn('id', $groups));
        }
        if ($promotions) {
            $query->orWhereHas('promotions', fn ($q) => $q->whereIn('id', $promotions));
        }

        return $query->pluck('id')->map(static fn ($id) => (int) $id)->all();
    }

    /**
     * @param int[] $ids
     * @return array<int, array<string, mixed>>
     */
    private function productsToArray(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return Product::query()
            ->with(['gallery', 'promotions'])
            ->whereIn('id', $ids)
            ->get()
            ->map(function (Product $product) {
                $regular = (float) $product->getPrice();
                $previous = (float) $product->getPrice(true);
                $promoPrice = $this->promotionPrice($product);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'images' => $product->gallery
                        ->map(fn (Photo $photo) => $photo->getUploadUrl())
                        ->values()
                        ->all(),
                    'url' => route('shop.product.view', $product->slug),
                    // При действующей акции цена по акции выводится как основная,
                    // базовая цена уходит в oldprice.
                    'price' => $promoPrice > 0 ? $promoPrice : $regular,
                    'preprice' => $promoPrice > 0 ? $regular : $previous,
                    'category' => $product->main_category_id,
                    'store' => true,
                    'pickup' => true,
                    'delivery' => true,
                    'code' => $product->code,
                ];
            })
            ->all();
    }

    /**
     * Цена по действующей акции (0 — акции нет или цена не задана).
     */
    private function promotionPrice(Product $product): float
    {
        $promotion = $product->promotion();
        if ($promotion === null || $promotion->pivot === null) {
            return 0.0;
        }

        $price = (float) $promotion->pivot->price;

        return $price > 0 ? $price : 0.0;
    }

    /**
     * @param int[] $ids
     * @return array<int, array<string, mixed>>
     */
    private function categoriesToArray(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Загружаем все категории одной выборкой, чтобы обойти дерево в памяти без N+1.
        $map = Category::query()
            ->select(['id', 'name', 'parent_id'])
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($ids as $id) {
            $this->appendCategory($result, $map, $id);
        }

        return array_values($result);
    }

    /**
     * @param array<int, array<string, mixed>> $result
     * @param Collection<int, Category> $map
     */
    private function appendCategory(array &$result, Collection $map, ?int $id): void
    {
        if ($id === null) {
            return;
        }

        /** @var Category|null $category */
        $category = $map->get($id);
        if ($category === null) {
            return;
        }

        // Родитель добавляется раньше дочернего (требование Yandex Market).
        if ($category->parent_id !== null) {
            $this->appendCategory($result, $map, (int) $category->parent_id);
        }

        $result[$id] = [
            'id' => $category->id,
            'name' => $category->name,
            'parent' => $category->parent_id !== null ? (int) $category->parent_id : null,
        ];
    }

    /**
     * @return array{name: string, company: string, description: string, url: string, preprice: bool}
     */
    private function getInfo(FeedEntity $feed): array
    {
        $trader = Trader::default();

        $name = !empty($feed->setTitle)
            ? $feed->setTitle
            : ($trader->name ?? '');

        return [
            'name' => $name,
            'company' => $name,
            'description' => !empty($feed->setDescription)
                ? $feed->setDescription
                : ($trader->description ?? ''),
            'url' => request()->host(),
            'preprice' => $feed->setPreprice,
        ];
    }
}
