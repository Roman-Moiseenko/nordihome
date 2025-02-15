<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends ShopController
{
    private ShopRepository $repository;
    private SlugRepository $slugs;
    private CacheRepository $caches;

    public function __construct(
        ShopRepository $repository,
        SlugRepository $slugs,
        CacheRepository $caches,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->slugs = $slugs;
        $this->caches = $caches;
    }

    public function index(): Factory|View|Application|null
    {
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;

        return view($this->route('catalog'), compact( 'title', 'description'));
    }

    public function view(Request $request, $slug)
    {

        $page = $request->has('page');
        if ((empty($request->all()) || (count($request->all()) == 1 && $page)) && $this->web->is_cache) {
            //Без фильтра берем кэш
           // set_time_limit(100);
            $cache_name = 'category-' . $slug . '-' . $request->string('page', '1')->value();
            return Cache::rememberForever($cache_name, function () use ($request, $slug) {
                return $this->caches->category_cache($request->all(), $slug);
            });
        } else {
            return $this->caches->category_cache($request->all(), $slug);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = [];
        if (empty($request['category'])) return \response()->json($result);
        try {
            $categories = $this->repository->getTree((int)$request['category']);
            /** @var Category $category */
            foreach ($categories as $category) {
                $result[] = $this->repository->toShopForSubMenu($category);
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            $result = ['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]];
        }
        return \response()->json($result);
    }


}
