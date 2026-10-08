<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Product\CreateProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\ForceDeleteProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\IndexProductQuery;
use App\Modules\Catalog\Application\Actions\Product\MassActionProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\RemoveProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\RenameProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\RestoreProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\SearchProductQuery;
use App\Modules\Catalog\Application\Actions\Product\TogglePublishedProductUseCase;
use App\Modules\Catalog\Application\Actions\Product\ToggleSaleProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\FilterProductIndexData;
use App\Modules\Catalog\Application\DTOs\Product\ProductCreateData;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Service\ProductService;
use App\Modules\Content\Application\Services\ProductSearchService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Log;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(
        ProductService                         $service,
        private readonly ProductSearchService  $productSearchService,
        private readonly SearchProductQuery    $searchProductQuery,
        private readonly IndexProductQuery     $indexProductQuery,
        private readonly CreateProductUseCase  $createProductUseCase,
        private readonly RenameProductUseCase  $renameProductUseCase,
        private readonly RemoveProductUseCase  $removeProductUseCase,
        private readonly RestoreProductUseCase $restoreProductUseCase,
        private readonly ForceDeleteProductUseCase $forceDeleteProductUseCase,
        private readonly ToggleSaleProductUseCase $toggleSaleProductUseCase,
        private readonly TogglePublishedProductUseCase $togglePublishedProductUseCase,
        private readonly MassActionProductUseCase $massActionProductUseCase,
    )
    {
        $this->service = $service;
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filterDto = FilterProductIndexData::validateAndCreate($request->all());
        $products = $this->indexProductQuery->execute($filterDto, $userPermission);

        return Inertia::render('Catalog/Product/Index', [
            'products' => $products,
            'filters' => $filterDto,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Catalog/Product/Create');
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = ProductCreateData::validateAndCreate($request->all());
        $product = $this->createProductUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.catalog.product.edit', $product->id)->with('success', 'Товар создан');
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
            'productId' => $id,
        ]);

    }

    public function rename(Product $product, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $this->renameProductUseCase->execute(
            $product->id,
            $request->string('name')->trim()->value(),
            $userPermission,
        );

        return redirect()->back()->with('success', 'Сохранено');
    }


    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeProductUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Товар помечен на удаление');
    }

    public function restore(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->restoreProductUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Товар восстановлен');
    }

    public function full_delete(int $id, UserPermission $userPermission): RedirectResponse
    {
            $this->forceDeleteProductUseCase->execute($id, $userPermission);
            return redirect()->back()->with('success', 'Товар удален полностью');
    }

    public function sale(Product $product, UserPermission $userPermission): RedirectResponse
    {
        $updated = $this->toggleSaleProductUseCase->execute($product->id, $userPermission);
        $message = $updated->notSale ? 'Товар убран из продажи' : 'Товар возвращен в продажу';

        return redirect()->back()->with('success', $message);
    }

    public function toggle(Product $product, UserPermission $userPermission): RedirectResponse //Переключение между Опубликовано и Черновик
    {
        $updated = $this->togglePublishedProductUseCase->execute($product->id, $userPermission);
        $message = $updated->isPublished() ? 'Товар опубликован' : 'Товар отправлен в черновики';

        return redirect()->back()->with('success', $message);
    }

    public function action(Request $request, UserPermission $userPermission): RedirectResponse
    {

            $this->massActionProductUseCase->execute(
                $request->string('action')->value(),
                $request->input('ids', []),
                $userPermission,
            );
            return redirect()->back()->with('success', 'Сохранено');

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
