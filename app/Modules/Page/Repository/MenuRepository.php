<?php

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Menu;
use App\Modules\Page\Entity\MenuItem;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class MenuRepository
{

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

        $categories = PostCategory::orderBy('name')->get();
        foreach ($categories as $category) {
            $url = route('shop.posts.view', $category->slug, false);
            if (!in_array($url, $items))
                $urls['posts']['items'][] = [
                    'name' => $category->name,
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
}
