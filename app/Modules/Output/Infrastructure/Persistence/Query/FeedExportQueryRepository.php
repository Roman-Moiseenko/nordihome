<?php

declare(strict_types=1);

namespace App\Modules\Output\Infrastructure\Persistence\Query;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Shared\Application\Actions\GetGalleryThumbUseCase;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Репозиторий чтения данных для выгрузки фида.
 *
 * Собирает товары из всех связанных с фидом сущностей (товары, теги,
 * категории, комнаты, группы, акции) с минимизацией N+1 и кеширует
 * результат на сутки. Модель Product здесь не используется — карточки
 * товаров загружаются через ProductIndexQueryRepository, а изображения
 * галереи — через GetGalleryThumbUseCase.
 */
class FeedExportQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly ProductIndexQueryRepository $productIndexQueryRepository,
        private readonly GetGalleryThumbUseCase $galleryThumbUseCase,
    ) {}

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

        $products = $this->productsToArray($ids, $feed->priceMin, $feed->priceMax);

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

        if (!$products && !$tags && !$categories && !$rooms && !$groups && !$promotions) {
            return [];
        }

        return DB::table('products')
            ->select('products.id')
            ->whereNull('products.deleted_at')
            ->where(function ($query) use ($products, $tags, $categories, $rooms, $groups, $promotions) {
                if ($products) {
                    $query->whereIn('products.id', $products);
                }
                if ($tags) {
                    $query->orWhereExists(fn ($q) => $q
                        ->selectRaw('1')
                        ->from('tags_products')
                        ->whereColumn('tags_products.product_id', 'products.id')
                        ->whereIn('tags_products.tag_id', $tags));
                }
                if ($categories) {
                    $query->orWhereExists(fn ($q) => $q
                        ->selectRaw('1')
                        ->from('categories_products')
                        ->whereColumn('categories_products.product_id', 'products.id')
                        ->whereIn('categories_products.category_id', $categories));
                    $query->orWhereIn('products.main_category_id', $categories);
                }
                if ($rooms) {
                    $query->orWhereExists(fn ($q) => $q
                        ->selectRaw('1')
                        ->from('rooms_products')
                        ->whereColumn('rooms_products.product_id', 'products.id')
                        ->whereIn('rooms_products.room_id', $rooms));
                }
                if ($groups) {
                    $query->orWhereExists(fn ($q) => $q
                        ->selectRaw('1')
                        ->from('groups_products')
                        ->whereColumn('groups_products.product_id', 'products.id')
                        ->whereIn('groups_products.group_id', $groups));
                }
                if ($promotions) {
                    $query->orWhereExists(fn ($q) => $q
                        ->selectRaw('1')
                        ->from('promotions_products')
                        ->whereColumn('promotions_products.product_id', 'products.id')
                        ->whereIn('promotions_products.promotion_id', $promotions));
                }
            })
            ->pluck('products.id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @param int[] $ids
     * @return array<int, array<string, mixed>>
     */
    private function productsToArray(array $ids, ?int $priceMin = null, ?int $priceMax = null): array
    {
        if (empty($ids)) {
            return [];
        }

        // Для фида используем розничные цены, поэтому ClientContext пустой (retail по умолчанию).
        $cards = $this->productIndexQueryRepository->loadProductCards($ids, new ClientContext());

        // Поля, которых нет в карточке товара: описание и основная категория.
        $meta = DB::table('products')
            ->whereIn('id', $ids)
            ->whereNull('deleted_at')
            ->select(['id', 'description', 'main_category_id'])
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($cards as $card) {
            $id = (int) $card['id'];
            $isSale = (bool) ($card['is_sale'] ?? true);
            $regular = $isSale ? (float) ($card['price'] ?? 0) : 0.0;
            $previous = $isSale ? (float) ($card['price_previous'] ?? 0) : 0.0;

            $promotion = $card['promotion'] ?? null;
            $promoPrice = ($promotion['has'] ?? false) ? (float) ($promotion['price'] ?? 0) : 0.0;

            // При действующей акции цена по акции выводится как основная,
            // базовая цена уходит в oldprice.
            $price = $promoPrice > 0 ? $promoPrice : $regular;

            // Фильтр по цене, если задан.
            if ($priceMin !== null && $price < $priceMin) {
                continue;
            }
            if ($priceMax !== null && $price > $priceMax) {
                continue;
            }

            $row = $meta->get($id);

            $result[] = [
                'id' => $id,
                'name' => $card['name'],
                'description' => $row?->description ?? '',
                'images' => $this->galleryThumbUseCase->execute($id, self::PHOTO_MODEL_TYPE),
                'url' => route('shop.product.view', $card['slug']),
                'price' => $price,
                'preprice' => $promoPrice > 0 ? $regular : $previous,
                'category' => $row?->main_category_id !== null ? (int) $row?->main_category_id : null,
                'store' => true,
                'pickup' => true,
                'delivery' => true,
                'code' => $card['code'],
            ];
        }

        return $result;
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
