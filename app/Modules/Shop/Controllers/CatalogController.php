<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Shop\Application\Queries\CategoryIndexQuery;
use App\Modules\Shop\Application\Queries\CategoryPageQuery;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\Shop\Repository\ViewRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class CatalogController extends ShopController
{
    private ViewRepository $views;

    public function __construct(
        ShopRepository $repository,
        ViewRepository $views,
        private readonly CategoryPageQuery $categoryPageQuery,
        private readonly CategoryIndexQuery $categoryIndexQuery,
    )
    {
        parent::__construct();
        $this->views = $views;
    }

    public function index(Request $request): Factory|View|Application|null
    {
        $data = $this->categoryIndexQuery->execute();

        return view('shop.catalog', [
            'pageData' => $data,
        ]);
    }

    public function view(Request $request, $slug): View|Factory|\Illuminate\View\View
    {
        //$start = microtime(true);

        $data = $this->categoryPageQuery->execute($slug, $request->all());
        //$time = (microtime(true) - $start);
        //\Log::info("CategoryPageQuery::execute время: " . number_format($time, 3, '.', '') . " сек");

        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }

    public function novelty(Request $request)
    {
        return $this->views->novelty($request->all());
    }


}
