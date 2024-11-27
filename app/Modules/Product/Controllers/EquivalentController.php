<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\EquivalentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EquivalentController extends Controller
{

    private EquivalentService $service;

    public function __construct(EquivalentService $service)
    {
        //$this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Equivalent::orderBy('name');
        if (!empty($category_id = $request['category_id'])) {
            $query = $query->where('category_id', '=', $category_id);
        }
        $equivalents = $this->pagination($query, $request, $pagination);
        $categories = Category::defaultOrder()->withDepth()->get();
        return view('admin.product.equivalent.index', compact('equivalents', 'categories', 'category_id', 'pagination'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'integer|exists:categories,id',
        ]);
        $equivalent = $this->service->register($request);
        return redirect()->route('admin.product.equivalent.show', compact('equivalent'));
    }

    public function show(Equivalent $equivalent)
    {
        //$_products = Product::orderBy('name')->where('main_category_id', $equivalent->category->id)->get();
        //дочерние категории
        $_products = Product::orderBy('name')
            ->where('main_category_id', '>=', $equivalent->category->_lft)
            ->where('main_category_id', '<=', $equivalent->category->_rgt)
            ->get();

        $products = [];
        //Очистка уже добавленных категорий
        foreach ($_products as $product) {
            if (!$equivalent->isProduct($product->id)) $products[] = $product;
        }
        return view('admin.product.equivalent.show', compact('equivalent', 'products'));
    }


    public function add_product(Request $request, Equivalent $equivalent)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);
        $this->service->addProductByIds($equivalent->id, (int)$request['product_id']);
        return redirect(route('admin.product.equivalent.show', $equivalent));
    }

    public function del_product(Equivalent $equivalent, Product $product)
    {
        $this->service->delProductByIds($equivalent->id, $product->id);
        return redirect(route('admin.product.equivalent.show', $equivalent));
    }

    public function rename(Request $request, Equivalent $equivalent)
    {
        $equivalent = $this->service->rename($request, $equivalent);
        return redirect(route('admin.product.equivalent.show', $equivalent));
    }

    public function destroy(Equivalent $equivalent)
    {
        $this->service->delete($equivalent);
        return redirect()->back();
    }

    //AJAX
    public function json_products(Equivalent $equivalent)
    {
        $result = [];
        foreach ($equivalent->products as $product) {
            $result[] = $product->name;
        }
        return \response()->json($result);
    }

}
