<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Packages;
use App\Modules\Catalog\Application\Actions\Product\SearchProductQuery;
use App\Modules\Catalog\Infrastructure\Models\Equivalent;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Repository\ProductRepository;
use App\Modules\Catalog\Request\ProductCreateRequest;
use App\Modules\Catalog\Service\ProductService;
use App\Modules\Content\Application\Services\ProductSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Log;

class ProductController extends Controller
{
    private ProductService $service;
    private ProductRepository $repository;

    public function __construct(
        ProductService                        $service,
        ProductRepository                     $repository,
        private readonly ProductSearchService $productSearchService,
        private readonly SearchProductQuery   $searchProductQuery,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $count = [
            'all' => Product::count(),
            'active' => Product::where('published', true)->count(),
            'draft' => Product::where('published', false)->count(),
            'not_sale' => Product::where('not_sale', true)->count(),
            'delete' => Product::onlyTrashed()->count(),
        ];

        $products = $this->repository->getIndex($request, $filters);
        return Inertia::render('Catalog/Product/Index', [
            'products' => $products,
            'filters' => $filters,
            'count' => $count,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Catalog/Product/Create');
    }

    public function store(ProductCreateRequest $request): RedirectResponse
    {
        $product = $this->service->createFull($request);
        return redirect()->route('admin.catalog.product.edit', $product)->with('success', 'Товар создан');
    }


    public function show(int $id): Response
    {
        return Inertia::render('Catalog/Product/Show', [
            'product' => Product::findOrFail($id)
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Catalog/Product/Edit', [
            'productId' => $id, //$this->repository->ProductWithToArray($product),
        /*    'dimensions' => array_select(Dimensions::TYPES),
            'complexities' => array_select(Packages::COMPLEXITIES),

            'equivalents' => Equivalent::orderBy('name')
                ->whereHas('category', function ($query) use ($product) {
                    $query->where('_lft', '<=', $product->category->_lft)
                        ->where('_rgt', '>=', $product->category->_rgt);
                })
                ->getModels(),*/
        ]);

    }

    public function rename(Product $product, Request $request): RedirectResponse
    {
        //Переименование товара для всех
        $product->update(['name' => $request->string('name')->trim()->value()]);
        return redirect()->back()->with('success', 'Сохранено');
    }


    public function destroy(int $id): RedirectResponse
    {
        $this->service->destroy(Product::findOrFail($id));
        return redirect()->back()->with('success', 'Товар помечен на удаление');
    }

    public function restore(int $id): RedirectResponse
    {
        $this->service->restore($id);
        flash('Товар восстановлен', 'success');
        return redirect()->back();
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
        try {
            $products = $this->searchProductQuery->execute(
                $request->string('search')->trim()->value()
            );

            return response()->json($products);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $result = $this->service->uploadByXlsx($file, $request->input('brand_id'));
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json($e->getMessage());
        }
    }

    public function find_parser(Request $request)
    {
        $result = $this->service->findParser($request->string('code')->value(), $request->integer('brand_id'));
        return response()->json($result);
    }

    public function search_add(Request $request): JsonResponse
    {
        $result = $this->productSearchService->search($request['search']);
        return response()->json($result);
    }

    /**
     * Список атрибутов товара для Модификации
     */
    public function attr_modification(Product $product): JsonResponse
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



}
