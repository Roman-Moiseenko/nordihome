<?php
declare(strict_types=1);

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;


use App\Modules\Shop\Application\Queries\CategoryIndexQuery;
use App\Modules\Shop\Application\Queries\CategoryPageQuery;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class CatalogController extends BaseController
{
    public function __construct(
        private readonly CategoryPageQuery $categoryPageQuery,
        private readonly CategoryIndexQuery $categoryIndexQuery,
    )
    {
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
        $data = $this->categoryPageQuery->execute($slug, $request->all());

        return view('shop.product.index', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }

    public function novelty(Request $request): View|Factory|\Illuminate\View\View
    {
        $data = $this->categoryPageQuery->executeNew($request->all());

        return view('shop.product.novelty', [
            'pageData' => $data,
            'request' => $request->all(),
        ]);
    }


}
