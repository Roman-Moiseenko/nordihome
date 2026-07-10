<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Shop\Application\Queries\CategoryPageQuery;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends ShopController
{
    private ShopRepository $repository;
    private ViewRepository $views;

    public function __construct(
        ShopRepository $repository,
        ViewRepository $views,
        private readonly CategoryPageQuery $categoryPageQuery,
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->views = $views;
    }

    public function index(Request $request): Factory|View|Application|null
    {
        $title = $this->web->categories_title;
        $description = $this->web->categories_desc;
        $categories = $this->repository->getChildren();
        return view('shop.catalog', compact('title', 'description', 'categories'));
    }

    public function view(Request $request, $slug)
    {
        $start = microtime(true);

        $data = $this->categoryPageQuery->execute($slug, $request->all());

        $time = (microtime(true) - $start);
        \Log::info("CategoryPageQuery::execute время: " . number_format($time, 3, '.', '') . " сек");

        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
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

    public function novelty(Request $request)
    {
        return $this->views->novelty($request->all());
    }


}
