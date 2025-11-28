<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Page;
use App\Modules\Page\Job\JobCacheCategory;
use App\Modules\Page\Job\JobCacheProduct;
use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Support\Facades\Cache;

class CacheService
{
//    private CacheRepository $cacheRepository;
    private ShopRepository $shopRepository;

    private Web $web;
    private MetaTemplateRepository $seo;


    public function __construct(
        ShopRepository $shopRepository,
        Settings $settings,
        MetaTemplateRepository $seo
    )
    {
        $this->shopRepository = $shopRepository;
        $this->web = $settings->web;
        $this->seo = $seo;
    }

    public function clearAll(): void
    {
        Cache::flush();
    }

    public function rebuildCache(): array
    {
        Cache::flush();
        //Кэш всех товаров для карточек
        $count['products'] = $this->rebuildProducts();
        $count['categories'] = $this->rebuildCategories();
        return $count;
    }

    public function rebuildProducts(): int
    {
        $products = Product::where('published', true)->where(function ($query) {
            $query->doesntHave('modification')->orHas('main_modification');
        })->get();
        foreach ($products as $product) {
            JobCacheProduct::dispatch($product->id);
        }
        return $products->count();
    }

    public function rebuildCategories(): int
    {
        $this->rebuildCategory('root');
        $categories = Category::get();
        foreach ($categories as $category) {
            $this->rebuildCategory($category->slug);
        }
        return $categories->count();
    }

    public function rebuildCategory(string $slug): void
    {
        JobCacheCategory::dispatch($slug);
    }

    public function rebuildPages(): int
    {
        $pages = Page::orderBy('name')->active()->get();

        foreach ($pages as $page) {
            Cache::forget('page-' . $page->slug);
            Cache::rememberForever('page-' . $page->slug, function () use ($page) {
                return $page->view($this->seo->seoFn());
            });
        }
        return $pages->count();
    }

}
