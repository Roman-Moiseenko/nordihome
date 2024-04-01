<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

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
        return $this->try_catch_admin(function () use($request) {
            $categories = Category::defaultOrder()->withDepth()->get();
            $published = $request['published'] ?? 'all';
            $query = Product::orderBy('name');

            if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
                $query->whereHas('categories', function ($q) use ($category_id, $published) {
                    $q->where('id', '=', $category_id);
                    if ($published == 'active') $q->where('published', '=', true);
                    if ($published == 'draft') $q->where('published', '=', false);
                })->orWhere('main_category_id', '=', $category_id);
            }
            if ($published == 'active') $query->where('published', '=', true);
            if ($published == 'draft') $query->where('published', '=', false);

            /*
            if (!empty($search = $request['search'])) {
                $query->where('code_search', 'LIKE', "%{$search}%")->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            }
             */
            $products = $this->pagination($query, $request, $pagination);

            return view('admin.product.product.index', compact('products', 'pagination',
                'categories', 'category_id', 'published'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $categories = Category::defaultOrder()->withDepth()->get();
            $menus = ProductHelper::menuAddProduct();
            $brands = Brand::orderBy('name')->get();
            $tags = Tag::orderBy('name')->get();
            $series = Series::orderBy('name')->get();
            return view('admin.product.product.create', compact('categories', 'menus', 'brands',
                'tags', 'series'));
        });
    }

    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $product = $this->service->create($request);
            return redirect()->route('admin.product.edit', compact('product'));
        });
    }

    public function show(Product $product)
    {
        return $this->try_catch_admin(function () use($product) {
            return view('admin.product.product.show', compact('product'));
        });
    }

    public function edit(Product $product)
    {
        return $this->try_catch_admin(function () use($product) {
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
        });
    }

    public function update(Request $request, Product $product)
    {
        return $this->try_catch_admin(function () use($request, $product) {
            $product = $this->service->update($request, $product);
            return redirect()->route('admin.product.edit', compact('product'));
        });
    }

    public function destroy(Product $product)
    {
        return $this->try_catch_admin(function () use($product) {
            $this->service->destroy($product);
            return redirect()->route('admin.product.product.index');
        });
    }

    public function toggle(Product $product) //Переключение между Опубликовано и Чернови
    {
        return $this->try_catch_admin(function () use($product) {
            if ($product->isPublished()) {
                $this->service->draft($product);
            } else {
                $this->service->published($product);
            }
            return redirect()->back();
        });
    }

    //AJAX
    public function search(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $result = [];
            $products = $this->repository->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->repository->toArrayForSearch($product);
            }
            return \response()->json($result);
        });
    }

    public function search_bonus(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $result = [];
            $bonus_ids = Bonus::orderBy('bonus_id')->pluck('bonus_id')->toArray();
            $products = $this->repository->search($request['search'], 10, $bonus_ids, false);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $this->repository->toArrayForSearch($product);
            }
            return \response()->json($result);
        });
    }

    public function attr_modification(Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($product) {
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
        });
    }

    public function get_images(Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($product) {
            $result = [];
            foreach ($product->photos as $photo) {
                $result[] = [
                    'id' => $photo->id,
                    'url' => $photo->getUploadUrl(),
                    'alt' => $photo->alt,
                ];
            }
            return \response()->json($result);
        });
    }

    public function del_image(Request $request, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($request, $product) {
            $this->service->delPhoto($request, $product);
            return \response()->json(true);
        });
    }

    public function up_image(Request $request, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($request, $product) {
            $this->service->upPhoto($request, $product);
            return \response()->json(true);
        });
    }

    public function down_image(Request $request, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($request, $product) {
            $this->service->downPhoto($request, $product);
            return \response()->json(true);
        });
    }

    public function alt_image(Request $request, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($request, $product) {
            $this->service->altPhoto($request, $product);
            return \response()->json(true);
        });
    }

    public function file_upload(Request $request, Product $product)
    {
        return $this->try_catch_ajax_admin(function () use($request, $product) {
            $this->service->addPhoto($request, $product);
            return \response()->json(true);
        });
    }

}
