<?php

declare(strict_types=1);

namespace App\Modules\Output\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Entity\Page;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Support\Facades\Cache;

/**
 * Репозиторий чтения данных для карты сайта (sitemap.xml).
 *
 * Собирает URL всех публичных страниц витрины (товары, категории, комнаты,
 * статические страницы, новости, акции) и кеширует результат на сутки.
 * Инвалидация кеша выполняется через CacheInvalidationRegistry::forgetSitemap().
 */
class SitemapQueryRepository
{
    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    public function getCachedPages(): array
    {
        return Cache::remember(
            CacheInvalidationRegistry::SITEMAP,
            now()->addDay(),
            fn (): array => $this->compute(),
        );
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function compute(): array
    {
        return array_merge(
            $this->products(),
            $this->categories(),
            $this->rooms(),
            $this->pages(),
            $this->posts(),
            $this->staticPages(),
            $this->promotions(),
        );
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function products(): array
    {
        return array_map(static function (Product $product) {
            return [
                'url' => route('shop.product.view', $product->slug),
                'date' => $product->updated_at->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Product::where('published', true)->where(function ($query) {
            $query->doesntHave('modification')->orHas('main_modification');
        })->getModels());
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function categories(): array
    {
        // Исключаем пустые категории.
        return array_map(static function (Category $category) {
            return [
                'url' => route('shop.category.view', $category->slug),
                'date' => now()->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Category::has('products')->where('published', true)->getModels());
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function rooms(): array
    {
        // Исключаем пустые комнаты.
        return array_map(static function (Room $room) {
            return [
                'url' => route('shop.room.view', $room->slug),
                'date' => now()->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Room::has('products')->where('published', true)->getModels());
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function pages(): array
    {
        return array_map(static function (Page $page) {
            return [
                'url' => route('shop.page.view', $page->slug),
                'date' => $page->updated_at->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Page::where('published', true)->getModels());
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function posts(): array
    {
        return array_map(static function (Post $post) {
            return [
                'url' => route('shop.post.view', $post->slug),
                'date' => $post->updated_at->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Post::where('published', true)->getModels());
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function staticPages(): array
    {
        return array_map(static fn (string $route) => [
            'url' => route($route),
            'date' => now()->format('c'),
            'changefreq' => 'daily',
        ], ['shop.home']);
    }

    /**
     * @return array<int, array{url: string, date: string, changefreq: string}>
     */
    private function promotions(): array
    {
        return array_map(static function (Promotion $promotion) {
            return [
                'url' => route('shop.promotion.view', $promotion->slug),
                'date' => $promotion->start_at->format('c'),
                'changefreq' => 'weekly',
            ];
        }, Promotion::where('status', 'started')->getModels());
    }
}
