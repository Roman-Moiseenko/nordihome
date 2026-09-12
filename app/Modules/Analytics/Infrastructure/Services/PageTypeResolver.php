<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Services;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use Illuminate\Http\Request;

/**
 * PageTypeResolver — определение типа страницы и entity_id по маршруту запроса.
 *
 * entity_id берётся из параметра маршрута (если он числовой) либо из атрибута
 * `analytics_entity_id`, который контроллер может положить после запроса к БД.
 */
final class PageTypeResolver
{
    /**
     * @var array<string, string>
     */
    private const array ROUTE_NAME_MAP = [
        'shop.product.view' => 'product',
        'shop.category.view' => 'category',
        'shop.post.view' => 'post',
        'shop.posts.view' => 'post',
        'shop.promotion.view' => 'promo',
        'shop.room.view' => 'room',
    ];

    /**
     * Маппинг типа страницы на модель, по которой ищется entity_id по slug.
     *
     * @var array<string, array{0: class-string, 1: string}>
     */
    private const array SLUG_MODELS = [
        'product' => [Product::class, 'slug'],
        'category' => [Category::class, 'slug'],
        'room' => [Room::class, 'slug'],
        'post' => [Post::class, 'slug'],
        'promo' => [Promotion::class, 'slug'],
    ];

    /**
     * @return array{0: string, 1: int|null}
     */
    public function resolve(Request $request): array
    {
        $route = $request->route();
        $routeName = $route ? (string) $route->getName() : '';

        if ($routeName === '') {
            return ['page', null];
        }

        if ($routeName === 'shop.home') {
            return ['home', null];
        }

        if (str_contains($routeName, 'search')) {
            return ['search', null];
        }

        $pageType = self::ROUTE_NAME_MAP[$routeName] ?? 'page';

        return [$pageType, $this->resolveEntityId($request)];
    }

    /**
     * Резолвит entity_id по slug-параметру маршрута.
     *
     * Используется после отработки контроллера, когда числовой id в маршруте
     * отсутствует (например, /shop/{slug} для товара). Атрибут
     * `analytics_entity_id` имеет приоритет и проверяется вызывающей стороной.
     */
    public function resolveEntityIdBySlug(string $pageType, Request $request): ?int
    {
        $slug = $request->route('slug');
        if (!is_string($slug) || $slug === '') {
            return null;
        }

        $map = self::SLUG_MODELS[$pageType] ?? null;
        if ($map === null) {
            return null;
        }

        [$modelClass, $column] = $map;

        /** @var object|null $entity */
        $entity = $modelClass::query()->where($column, $slug)->first(['id']);

        $id = $entity->id ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function resolveEntityId(Request $request): ?int
    {
        $attribute = $request->attributes->get('analytics_entity_id');
        if (is_numeric($attribute)) {
            return (int) $attribute;
        }

        foreach (['id', 'entity_id'] as $key) {
            $value = $request->route($key);
            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }
}
