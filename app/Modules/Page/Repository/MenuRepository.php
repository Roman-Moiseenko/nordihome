<?php

namespace App\Modules\Page\Repository;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Menu;
use App\Modules\Page\Entity\MenuItem;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Repository\CategoryRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class MenuRepository
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getMenus(Request $request): Arrayable
    {
        return Menu::orderBy('id')->get()->map(fn(Menu $menu) => $this->MenuWithArray($menu));
    }

    private function MenuWithArray(Menu $menu): array
    {
        return array_merge($menu->toArray(), [
            'items' => $menu->items()->get()->map(fn(MenuItem $item) => $item->toArray())
        ]);
    }

    public function getUrls(Menu $menu): array
    {
        $urls = [
            'page' => [
                'name' => 'Страницы',
                'items' => [],
            ],
            'posts' => [
                'name' => 'Рубрики',
                'items' => [],
            ],
            'post' => [
                'name' => 'Записи',
                'items' => [],
            ],
            'shop' => [
                'name' => 'Магазин',
                'items' => [],
            ],
            'category' => [
                'name' => 'Категории',
                'items' => [],
            ],
        ];

        $items = $menu->items()->get()->map(fn(MenuItem $item) => $item->url)->toArray();

        $pages = Page::orderBy('name')->active()->get();
        foreach ($pages as $page) {
            $url = route('shop.page.view', $page->slug, false);
            if (!in_array($url, $items))
                $urls['page']['items'][] = [
                    'name' => $page->name,
                    'url' => $url,
                ];
        }

        $post_categories = PostCategory::orderBy('name')->get();
        foreach ($post_categories as $post_category) {
            $url = route('shop.posts.view', $post_category->slug, false);
            if (!in_array($url, $items))
                $urls['posts']['items'][] = [
                    'name' => $post_category->name,
                    'url' => $url,
                ];
        }

        $posts = Post::orderBy('name')->active()->get();
        foreach ($posts as $post) {
            $url = route('shop.post.view', $post->slug, false);
            if (!in_array($url, $items))
                $urls['post']['items'][] = [
                    'name' => $post->name,
                    'url' => $url,
                ];
        }

        //FREE
        $url = route('shop.category.index', [], false);
        if (!in_array($url, $items))
            $urls['shop']['items'][] = [
                'name' => 'Каталог',
                'url' => $url,
            ];
        $groups = Group::orderBy('name')->active()->get();
        foreach ($groups as $group) {
            $url = route('shop.group.view', $group->slug, false);
            $urls['shop']['items'][] = [
                'name' => $group->name,
                'url' => $url,
            ];
        }

        $promotions = Promotion::orderBy('name')->active()->get();
        foreach ($promotions as $promotion) {
            $url = route('shop.promotion.view', $promotion->slug, false);
            $urls['shop']['items'][] = [
                'name' => $promotion->name,
                'url' => $url,
            ];
        }
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $url = route('shop.category.view', $category['slug'], false);
            $urls['category']['items'][] = [
                'name' => $category['name'],
                'url' => $url,
            ];
        }

      /*
        $url = route('shop.home', [], false);
        if (!in_array($url, $items))
            $urls['shop']['items'][] = [
                'name' => 'Главная',
                'url' => $url,
            ];
        */

        return $urls;
    }

    public function getItems(Menu $menu): array
    {
        return [
            'items' =>
                $menu->items()->get()->map(fn(MenuItem $item) => $item->toArray())
        ];
    }

    private function getCategories(): array
    {
        return array_map(function (Category $category) {
            $_depth = str_repeat('-', $category->depth);
            return [
                'slug' => $category->slug,
                'name' => trim($_depth . ' ' . $category->name),
            ];
        }, Category::defaultOrder()->withDepth()->getModels());
    }
}
