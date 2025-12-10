<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Repository\CacheRepository;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\SlugRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends ShopController
{
    private ShopRepository $repository;
    private CacheRepository $caches;
    private ViewRepository $views;

    public function __construct(
        ShopRepository $repository,
        CacheRepository $caches,
        ViewRepository $views,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->caches = $caches;
        $this->views = $views;
    }

    public function index(Request $request): Factory|View|Application|null
    {
        return $this->views->root($request->all());
/*
        $url_page = $this->route('shop.category.index');
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;
        $categories = $this->repository->getChildren();
        return view($this->route('catalog'), compact( 'title', 'description', 'categories', 'url_page'));*/
    }

    public function view(Request $request, $slug)
    {

        $page = $request->has('page');

        if ((empty($request->all()) || (count($request->all()) == 1 && $page)) && $this->web->is_cache) {
            return $this->caches->category($request->all(), $slug);
        } else {
            return $this->views->category($request->all(), $slug);
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
