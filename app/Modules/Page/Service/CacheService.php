<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Job\JobCacheCategory;
use App\Modules\Page\Job\JobCacheProduct;
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


    public function __construct(
        ShopRepository $shopRepository,
        Settings $settings,

    )
    {
        $this->shopRepository = $shopRepository;
        $this->web = $settings->web;
    }

    public function clearAll(): void
    {
        Cache::flush();
    }

    public function rebuildCache(): array
    {
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
        $categories = Category::get();
        foreach ($categories as $category) {
            $this->rebuildCategory($category);
        }
        return $categories->count();
    }

    public function rebuildCategory(Category $category): void
    {
        $count_products = $this->shopRepository->ProductsByCategory($category)->count();
        $pages = (int)ceil($count_products / $this->web->paginate);
        for($i = 1; $i <= $pages; $i++) {
            JobCacheCategory::dispatch($i, $category->slug);
        }
    }



}
