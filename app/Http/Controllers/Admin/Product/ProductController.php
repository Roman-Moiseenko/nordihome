<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Attribute;
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
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    private ProductService $service;
    private Options $options;
    private ProductRepository $repository;
    private mixed $pagination;

    public function __construct(ProductService $service, Options $options, ProductRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->options = $options;
        $this->repository = $repository;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();

        $published = $request['published'] ?? 'all';
        $query = Product::orderBy('name');


        if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
            $query->whereHas('categories', function ($q) use ($category_id, $published) {
                $q->where('id', '=', $category_id);
                if ($published == 'active') $q->where('published', '=', true);
                if ($published == 'draft') $q->where('published', '=', false);
                //TODO выбрать товары из всех подкатегорий
            })->orWhere('main_category_id', '=', $category_id);
        }
        if ($published == 'active') $query->where('published', '=', true);
        if ($published == 'draft') $query->where('published', '=', false);

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $products = $query->paginate($pagination);
            $products->appends(['p' => $pagination]);
        } else {
            $products = $query->paginate($this->pagination);
        }

        return view('admin.product.product.index', compact('products', 'pagination',
            'categories', 'category_id', 'published'));
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
        $equivalents = Equivalent::orderBy('name')->where('category_id', '=', $product->main_category_id)->get();
        return view('admin.product.product.edit', compact('product', 'categories',
            'menus', 'brands', 'tags', 'groups', 'options', 'equivalents', 'series'));

    }

    public function update(Request $request, Product $product)
    {
        $product = $this->service->update($request, $product);
        return redirect()->route('admin.product.edit', compact('product'));

    }

    public function destroy(Product $product)
    {
        try {
            $this->service->destroy($product);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return redirect(route('admin.product.product.index'));
    }

    public function toggle(Product $product) //Переключение между Опубликовано и Чернови
    {
        try {
            if ($product->isPublished()) {
                $this->service->draft($product);
            } else {
                $this->service->published($product);
            }
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return redirect()->back();
    }

    //AJAX



    public function search(Request $request)
    {
        $result = [];
        try {
            $products = $this->repository->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->repository->toArrayForSearch($product);
            }
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }
        return \response()->json($result);
    }

    public function search_bonus(Request $request)
    {
        $result = [];
        try {
            $bonus_ids = Bonus::orderBy('bonus_id')->pluck('bonus_id')->toArray();
            $products = $this->repository->search($request['search'], 10, $bonus_ids, false);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->repository->toArrayForSearch($product);
            }
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
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
            ];
        }
        return \response()->json($result);
    }

    public function del_image(Request $request, Product $product)
    {
        $this->service->delPhoto($request, $product);
    }

    public function up_image(Request $request, Product $product)
    {
        $this->service->upPhoto($request, $product);
    }

    public function down_image(Request $request, Product $product)
    {
        $this->service->downPhoto($request, $product);
    }

    public function alt_image(Request $request, Product $product)
    {
        $this->service->altPhoto($request, $product);
    }

    public function file_upload(Request $request, Product $product)
    {
        try {
            $this->service->addPhoto($request, $product);
        } catch (\Throwable $e) {

        }

    }

}
