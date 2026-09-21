<?php
declare(strict_types=1);

namespace App\Modules\Output\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Entity\Page;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Discount\Infrastructure\Models\Promotion;

class SitemapXmlController extends Controller
{

    public function index()
    {
        $pages = array_merge(
            $this->products(),
            $this->categories(),
            $this->rooms(),
            $this->pages(),
            $this->posts(),
            $this->static(),
            $this->promotions(),
        //TODO Метки и другие умные фильтры
        );
        $content = view('output.sitemap', compact('pages'))->render();
        ob_end_clean();
        return response($content)->header('Content-Type','text/xml');
    }

    private function products(): array
    {
        return array_map(function (Product $product) {
            return [
                'url' => route('shop.product.view', $product->slug),
                'date' => $product->updated_at->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Product::where('published', true)->where(function ($query) {
            $query->doesntHave('modification')->orHas('main_modification');
        })->getModels());
    }

    private function categories(): array
    {
        //Исключить пустые категории
        return array_map(function (Category $category) {
            return [
                'url' => route('shop.category.view', $category->slug),
                'date' => now()->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Category::has('products')->where('published', true)->getModels());
    }

    private function rooms(): array
    {
        //Исключить пустые категории
        return array_map(function (Room $room) {
            return [
                'url' => route('shop.room.view', $room->slug),
                'date' => now()->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Room::has('products')->where('published', true)->getModels());
    }


    private function pages(): array
    {
        return array_map(function (Page $page) {
            return [
                'url' => route('shop.page.view', $page->slug),
                'date' => $page->updated_at->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Page::where('published', true)->getModels());
    }

    private function posts(): array
    {
        return array_map(function (Post $post) {
            return [
                'url' => route('shop.post.view', $post->slug),
                'date' => $post->updated_at->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Post::where('published', true)->getModels());
    }


    private function static(): array
    {
        return array_map(function ($item) {
            return [
                'url' => route($item),
                'date' => now()->format('c'),
                'changefreq' => 'daily'
            ];
        }, ['shop.home']);
    }

    private function promotions(): array
    {
        return array_map(function (Promotion $promotion) {
            return [
                'url' => route('shop.promotion.view', $promotion->slug),
                'date' => $promotion->start_at->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Promotion::where('status', 'started')->getModels());
    }
}
