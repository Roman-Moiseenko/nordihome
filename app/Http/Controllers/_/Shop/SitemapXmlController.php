<?php
declare(strict_types=1);

namespace App\Http\Controllers\_\Shop;

use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Page;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use function response;
use function view;

class SitemapXmlController extends Controller
{

    public function index()
    {
        //TODO Формируем массив данных формата
        // Сделать по индексным файлам

        $pages = array_merge(
            $this->products(),
            $this->categories(),
            $this->pages(),
            $this->static(),
            $this->promotions(),
        );
        $content = view('shop.sitemap', compact('pages'))->render();
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
        }, Product::where('published', true)->getModels());
    }

    private function categories(): array
    {
        return array_map(function (Category $category) {
            return [
                'url' => route('shop.category.view', $category->slug),
                'date' => now()->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Category::getModels());
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

    private function static(): array
    {
        return array_map(function ($item) {
            return [
                'url' => route($item),
                'date' => now()->format('c'),
                'changefreq' => 'daily'
            ];
        }, ['home']);
    }

    private function promotions(): array
    {
        return array_map(function (Promotion $promotion) {
            return [
                'url' => route('shop.promotion.view', $promotion->slug),
                'date' => $promotion->start_at->format('c'),
                'changefreq' => 'weekly'
            ];
        }, Promotion::where('published', true)->where('active', true)->getModels());
    }
}
