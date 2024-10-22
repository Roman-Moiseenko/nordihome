<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Helper\ProductHelper;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\Deprecated;

class ProductController extends Controller
{
    private ProductService $service;
    private Options $options;
    private ProductRepository $repository;

    public function __construct(ProductService $service, Options $options, ProductRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->options = $options;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $query = $this->repository->getFilter($request, $filters);
        $products = $this->pagination($query, $request, $pagination);

        return view('admin.product.product.index', compact('products', 'pagination',
            'categories', 'filters'));
    }

    public function create(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $menus = ProductHelper::menuAddProduct();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $series = Series::orderBy('name')->get();
        return view('admin.product.product.create', compact('categories', 'menus', 'brands',
            'tags', 'series'));
    }

    public function store(Request $request)
    {
        $product = $this->service->create($request);
        return redirect()->route('admin.product.edit', compact('product'));
    }

    public function fast_create(Request $request)
    {
        $product = $this->service->create($request);
        $product->pricesRetail()->create([
            'value' => $request->integer('price'),
            'founded' => 'Создано из заказа',
        ]);
        $product->pricesPre()->create([
            'value' => $request->integer('price'),
            'founded' => 'Создано из заказа',
        ]);
        return response()->json($product->id);
    }

    public function show(Product $product)
    {
        return view('admin.product.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $menus = ProductHelper::menuUpdateProduct();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $series = Series::orderBy('name')->get();
        $groups = AttributeGroup::orderBy('name')->get();
        $options = $this->options;
        $equivalents = Equivalent::orderBy('name')
            ->whereHas('category', function ($query) use ($product) {
                $query->where('_lft', '<=', $product->category->_lft)
                    ->where('_rgt', '>=', $product->category->_rgt);
            })
            ->get();

        //$equivalents = Equivalent::orderBy('name')->where('category_id', $product->main_category_id)->get();

        return view('admin.product.product.edit', compact('product', 'categories',
            'menus', 'brands', 'tags', 'groups', 'options', 'equivalents', 'series'));
    }

    #[Deprecated]
    public function update(Request $request, Product $product)
    {
        $product = $this->service->update($request, $product);
        return redirect()->route('admin.product.edit', compact('product'));
    }

    public function destroy(Product $product)
    {
        $this->service->destroy($product);
        return redirect()->route('admin.product.product.index');
    }

    public function toggle(Product $product) //Переключение между Опубликовано и Чернови
    {
        if ($product->isPublished()) {
            $this->service->draft($product);
        } else {
            $this->service->published($product);
        }
        return redirect()->back();
    }

    //AJAX

    public function action(Request $request)
    {
        //return \response()->json(['error' => $request['data']]);
        try {
            $data = json_decode($request['data'], true);
            $this->service->action($data['action'], $data['ids']);
            return \response()->json(true);
        } catch (\Throwable $e) {
            return \response()->json(['error' => $e->getMessage()]);
        }

    }

    public function search(Request $request)
    {
        $result = [];
        $products = $this->repository->search($request['search']);
        /** @var Product $product */
        foreach ($products as $product) {
            $result[] = $this->repository->toArrayForSearch($product);
        }
        return \response()->json($result);
    }

    public function search_add(Request $request)
    {
        $result = [];
        $products = $this->repository->search($request['search']);

        //Применить map()
        /** @var Product $product */
        foreach ($products as $product) {
            if (!$request->has('published') || ($request->has('published') && $product->isPublished()))
                $result[] = $this->repository->toArrayForSearch($product);
        }

        return \response()->json($result);
    }


    public function search_bonus(Request $request)
    {
        $result = [];
        $bonus_ids = Bonus::orderBy('bonus_id')->pluck('bonus_id')->toArray();
        $products = $this->repository->search($request['search'], 10, $bonus_ids, false);
        /** @var Product $product */
        foreach ($products as $product) {
            $result[] = $this->repository->toArrayForSearch($product);
        }
        return \response()->json($result);
    }

    public function attr_modification(Product $product)
    {
        $result = [];
        foreach ($product->prod_attributes as $attribute) {
            if ($attribute->isVariant() && !$attribute->multiple) {
                $result[] = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                ];
            }
        }
        return \response()->json($result);
    }

    public function get_images(Product $product)
    {
        $result = [];
        foreach ($product->photos as $photo) {
            $result[] = [
                'id' => $photo->id,
                'url' => $photo->getUploadUrl(),
                'alt' => $photo->alt,
                'sort' => $photo->sort,
            ];
        }
        return \response()->json($result);
    }

    public function del_image(Request $request, Product $product)
    {
        $this->service->delPhoto($request, $product);
        return \response()->json(true);
    }

    public function up_image(Request $request, Product $product)
    {
        $this->service->upPhoto($request->integer('photo_id'), $product);
        return \response()->json(true);
    }

    public function down_image(Request $request, Product $product)
    {
        $this->service->downPhoto($request->integer('photo_id'), $product);
        return \response()->json(true);
    }

    public function alt_image(Request $request, Product $product)
    {
        $this->service->altPhoto($request, $product);
        return \response()->json(true);
    }

    public function move_image(Request $request, Product $product)
    {
        try {
            $this->service->movePhoto($request, $product);
            return \response()->json(true);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }

    }

    public function file_upload(Request $request, Product $product)
    {
        $this->service->addPhoto($request, $product);
        return \response()->json(true);
    }

}
