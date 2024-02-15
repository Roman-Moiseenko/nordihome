<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\EquivalentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class EquivalentController extends Controller
{

    private mixed $pagination;
    private EquivalentService $service;

    public function __construct(EquivalentService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->pagination = Config::get('shop-config.p-list');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $query = Equivalent::orderBy('name');

            if (!empty($category_id = $request['category_id'])) {
                $query = $query->where('category_id', '=', $category_id);
            }

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $equivalents = $query->paginate($pagination);
                $equivalents->appends(['p' => $pagination]);
            } else {
                $equivalents = $query->paginate($this->pagination);
            }

            $categories = Category::defaultOrder()->withDepth()->get();
            return view('admin.product.equivalent.index', compact('equivalents', 'categories', 'category_id', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'integer|exists:categories,id',
        ]);
        try {
            $equivalent = $this->service->register($request);
            return redirect()->route('admin.product.equivalent.show', compact('equivalent'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Equivalent $equivalent)
    {
        try {
            $_products = Product::orderBy('name')->where('main_category_id', '=', $equivalent->category->id)->get();
            $products = [];
            //Очистка уже добавленных категорий
            foreach ($_products as $product) {
                if (!$equivalent->isProduct($product->id)) $products[] = $product;
            }
            return view('admin.product.equivalent.show', compact('equivalent', 'products'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }


    public function add_product(Request $request, Equivalent $equivalent)
    {

        $request->validate([
            'product_id' => 'required|integer',
        ]);
        try {
            $this->service->addProductByIds($equivalent->id, (int)$request['product_id']);
            return redirect(route('admin.product.equivalent.show', $equivalent));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function del_product(Equivalent $equivalent, Product $product)
    {
        try {
            $this->service->delProductByIds($equivalent->id, $product->id);
            return redirect(route('admin.product.equivalent.show', $equivalent));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function rename(Request $request, Equivalent $equivalent)
    {
        try {
            $equivalent = $this->service->rename($request, $equivalent);
            return redirect(route('admin.product.equivalent.show', $equivalent));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Equivalent $equivalent)
    {
        try {
            $this->service->delete($equivalent);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    //AJAX

    public function json_products(Equivalent $equivalent)
    {

        $result = [];
        try {
            foreach ($equivalent->products as $product) {
                $result[] = $product->name;
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
        return \response()->json($result);
    }

}
