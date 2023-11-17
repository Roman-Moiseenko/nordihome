<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    private ProductRepository $repository;
    private CategoryRepository $categories;

    public function __construct(ProductRepository $repository, CategoryRepository $categories)
    {
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function view($slug/*Product $product*/)
    {
        try {
            $product = $this->repository->getBySlug($slug);
            return view('shop.product', compact('product'));
        } catch (\Throwable $e) {
            $product = null;
            flash($e->getMessage(), 'danger');
            return redirect()->route('home'); //'error.403', ['message' => $e->getMessage()]
        }
    }

    public function search(Request $request)
    {
        $result = [];
        if (empty($request['search'])) return ;
        try {
            $categories = $this->categories->search($request['search'], 5);
            $products = $this->repository->search($request['search'], 5);
            /** @var Category $category */
            foreach ($categories as $category) {
                $result[] = $this->categories->toShopForSearch($category);
            }

            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->repository->toShopForSearch($product);
            }
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }
        return \response()->json($result);
    }

}
