<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Helper\ProductHelper;
use App\Modules\Product\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    private ProductService $service;
    private Options $options;

    public function __construct(ProductService $service, Options $options)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->options = $options;
    }

    public function index(Request $request)
    {
        $products = Product::orderBy('id')->get();
        return view('admin.product.product.index', compact('products'));
    }

    public function create(Request $request)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $menus = ProductHelper::menuAddProduct();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.product.product.create', compact('categories', 'menus', 'brands', 'tags'));
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
        $groups = AttributeGroup::orderBy('name')->get();
        $options = $this->options;
        $equivalents = Equivalent::orderBy('name')->where('category_id', '=', $product->main_category_id)->get();
        return view('admin.product.product.edit', compact('product', 'categories', 'menus', 'brands', 'tags', 'groups', 'options', 'equivalents'));

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

    //AJAX для работы с Фото

    public function search(Request $request)
    {
        //TODO В Repository
        $data = $request['search'];
        $result = [];
        try {
            $products = Product::orderBy('name')->where(function ($query) use ($data) {
                $query->where('code', 'LIKE', "%{$data}%")
                    ->orWhere('name', 'LIKE', "%{$data}%");
            })->take(10)->get();

            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->getImage(),
                    'price' => $product->lastPrice->value,
                ];
            }

        } catch (\Throwable $e) {
            $result = $e->getMessage();
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
