<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Job\CacheCategory;
use App\Modules\Page\Job\CacheProductCard;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Entity\Web;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use Illuminate\Support\Facades\Cache;

class CacheService
{
//    private CacheRepository $cacheRepository;
    private ShopRepository $shopRepository;
    private Web $web;

    public function __construct(ShopRepository $shopRepository, Settings $settings)
    {
      //  $this->cacheRepository = $cacheRepository;
        $this->shopRepository = $shopRepository;
        $this->web = $settings->web;
    }

    public function clearAll(): void
    {
        Cache::flush();
    }

    public function rebuildCache(): array
    {
        $this->clearAll();
        //Кэш всех товаров для карточек
        $products = Product::where('published', true)->get();
        foreach ($products as $product) {
            CacheProductCard::dispatch($product->id);

        }
        $count['products'] = $products->count();
        $categories = Category::get();
        $count['categories'] = $categories->count();
        foreach ($categories as $category) {
            $count_products = $this->shopRepository->ProductsByCategory($category)->count();
            $pages = (int)ceil($count_products / $this->web->paginate);
            for($i = 1; $i <= $pages; $i++) {
                CacheCategory::dispatch($i, $category->slug);
            }
        }
        return $count;
    }
}
