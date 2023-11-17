<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function view(Request $request, $slug)
    {
        try {
            $category = $this->repository->getBySlug($slug);
            return view('shop.catalog', compact('category'));
        } catch (\Throwable $e) {
            $category = null;
            flash($e->getMessage(), 'danger');
            return redirect()->route('home'); //'error.403', ['message' => $e->getMessage()]
        }
    }

    public function search(Request $request)
    {
        $result = [];
        if (empty($request['category'])) return ;
        try {
            $categories = $this->repository->getTree((int)$request['category']);

            /** @var Category $category */
                        foreach ($categories as $category) {
                            $result[] = $this->repository->toShopForSubMenu($category);
                        }

        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }
        return \response()->json($result);
    }
}
