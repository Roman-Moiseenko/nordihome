<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Series;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Helper\ProductHelper;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Request\ProductCreateRequest;
use App\Modules\Product\Service\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class ProductController extends Controller
{
    private ProductService $service;
    private Options $options;
    private ProductRepository $repository;
    private CategoryRepository $categories;

    public function __construct(
        ProductService $service,
                                Options $options,
        ProductRepository $repository,
        CategoryRepository $categories,
    )
    {
        $this->middleware(['auth:admin']);
        $this->middleware(['can:product'])->except(['rename']);
        $this->service = $service;
        $this->options = $options;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function index(Request $request): Response
    {
        $categories = $this->categories->forFilters();;
        $count = [
            'all' => Product::count(),
            'active' => Product::where('published', true)->count(),
            'draft' => Product::where('published', false)->count(),
            'not_sale' => Product::where('not_sale', true)->count(),
            'delete' => Product::onlyTrashed()->count(),
        ];

        $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Product/Index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
            'count' => $count,
        ]);

    }

    public function create(Request $request): Response
    {
        return Inertia::render('Product/Product/Create', [
            'categories' => $this->categories->forFilters(),
            'brands' => Brand::orderBy('name')->getModels(),
            'tags' => Tag::orderBy('name')->getModels(),
            'series' => Series::orderBy('name')->getModels(),
            'country' => Country::orderBy('name')->getModels(),
            'vat' => VAT::orderBy('value')->getModels(),
            'measuring' => Measuring::orderBy('name')->getModels(),
            'markingType' => MarkingType::orderBy('name')->getModels(),
            'distributors' => Distributor::orderBy('name')->getModels(),
        ]);
    }

    public function store(ProductCreateRequest $request): RedirectResponse
    {
        try {
            $product = $this->service->createFull($request);
            return redirect()->route('admin.product.edit', $product)->with('success', 'Товар создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function fast_create(Request $request): JsonResponse
    {
        $product = $this->service->create($request);
        if ($request->integer('price') > 0) {
            $product->pricesRetail()->create([
                'value' => $request->integer('price'),
                'founded' => 'Создано из заказа',
            ]);
            $product->pricesPre()->create([
                'value' => $request->integer('price'),
                'founded' => 'Создано из заказа',
            ]);
        }
        return response()->json($product->id);
    }

    public function show(Product $product): Response
    {
        return Inertia::render('Product/Product/Show', [
            'product' => $product
        ]);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Product/Product/Edit', [
            'product' => $this->repository->ProductWithToArray($product),
            'categories' => $this->categories->forFilters(),
            'brands' => Brand::orderBy('name')->getModels(),
            'tags' => Tag::orderBy('name')->getModels(),
            'series' => Series::orderBy('name')->getModels(),
            'groups' => AttributeGroup::orderBy('name')->get(),
            'country' => Country::orderBy('name')->getModels(),
            'vat' => VAT::orderBy('value')->getModels(),
            'measuring' => Measuring::orderBy('name')->getModels(),
            'markingType' => MarkingType::orderBy('name')->getModels(),
            'distributors' => Distributor::orderBy('name')->getModels(),
            'dimensions' => array_select(Dimensions::TYPES),
            'frequencies' => array_select(Product::FREQUENCIES),
            'equivalents' => Equivalent::orderBy('name')
                ->whereHas('category', function ($query) use ($product) {
                    $query->where('_lft', '<=' ,$product->category->_lft)
                        ->where('_rgt', '>=' ,$product->category->_rgt);
                })
                ->getModels(),
        ]);

    }

    public function rename(Product $product, Request $request): RedirectResponse
    {
        //Переименование товара для всех
        $product->update(['name' => $request->string('name')->trim()->value()]);
        return redirect()->back()->with('success', 'Сохранено');
    }

    #[Deprecated]
    public function update(Request $request, Product $product)
    {
        $product = $this->service->update($request, $product);
        return redirect()->route('admin.product.edit', compact('product'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->service->destroy($product);
        return redirect()->back();//route('admin.product.index');
    }

    public function restore(int $id): RedirectResponse
    {
        $this->service->restore($id);
        flash('Товар восстановлен', 'success');
        return redirect()->back();//route('admin.product.index');
    }

    public function full_delete(int $id): RedirectResponse
    {
        try {
            $this->service->full_delete($id);
            return redirect()->back()->with('success', 'Товар удален полностью');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function sale(Product $product): RedirectResponse
    {
        $product->not_sale = !$product->not_sale;
        $product->save();
        if ($product->isSale()) {
            $message = 'Товар возвращен в продажу';
        } else {
            $message = 'Товар убран из продажи';
        }
        return redirect()->back()->with('success', $message);
    }

    public function toggle(Product $product): RedirectResponse //Переключение между Опубликовано и Черновик
    {
        if ($product->isPublished()) {
            $this->service->draft($product);
            $message = 'Товар отправлен в черновики';
        } else {
            $this->service->published($product);
            $message = 'Товар опубликован';
        }
        return redirect()->back()->with('success', $message);;
    }

    public function action(Request $request): RedirectResponse
    {
        try {
            $this->service->action($request->string('action')->value(), $request->input('ids'));
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function search(Request $request): JsonResponse
    {
       //return \response()->json(true);
        $result = [];

        try {
            $products = $this->repository->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $product->toArrayForSearch();
            }
            return \response()->json($result);
        } catch (\Throwable $e) {
            return \response()->json(['error' => $e->getMessage(),]);
        }
    }

    public function search_add(Request $request)
    {
        $result = [];
        $products = $this->repository->search($request['search']);

        //Применить map()
        /** @var Product $product */
        foreach ($products as $product) {
            if (!$request->has('published') || ($request->has('published') && $product->isPublished()))
                $result[] = $product->toArrayForSearch();
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
        return redirect()->back()->with('success', 'Удалено');
    }

    public function up_image(Request $request, Product $product)
    {
        $this->service->upPhoto($request->integer('photo_id'), $product);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_image(Request $request, Product $product)
    {
        $this->service->downPhoto($request->integer('photo_id'), $product);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_image(Request $request, Product $product)
    {
        $this->service->setPhoto($request, $product);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function move_image(Request $request, Product $product)
    {
        try {
            $this->service->movePhoto($request, $product);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function add_image(Request $request, Product $product)
    {
        try {
            $photo = $this->service->addPhoto($request, $product);
            return \response()->json([
                'id' => $photo->id,
                'url' => $photo->getUploadUrl(),
            ]);
        } catch (\Throwable $e) {
            return \response()->json($e->getMessage());
        }
    }


    //Vue3 Edit

    public function edit_common(ProductCreateRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editCommon($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }


    public function edit_description(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editDescription($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit_dimensions(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editDimensions($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit_video(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editVideo($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit_attribute(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editAttribute($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit_management(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editManagement($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function edit_equivalent(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editEquivalent($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function edit_related(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editRelated($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function edit_bonus(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editBonus($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function edit_composite(Request $request, Product $product): RedirectResponse
    {
        try {
            $this->service->editComposite($product, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
