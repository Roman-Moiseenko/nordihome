<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Brand\CreateBrandUseCase;
use App\Modules\Catalog\Application\Actions\Brand\IndexBrandQuery;
use App\Modules\Catalog\Application\Actions\Brand\ListBrandUseCase;
use App\Modules\Catalog\Application\Actions\Brand\RemoveBrandUseCase;
use App\Modules\Catalog\Application\Actions\Brand\UpdateBrandUseCase;
use App\Modules\Catalog\Application\Actions\Brand\ViewBrandQuery;
use App\Modules\Catalog\Application\Actions\Product\ListProductByBrandUseCase;
use App\Modules\Catalog\Application\DTOs\Brand\BrandCreateData;
use App\Modules\Catalog\Application\DTOs\Brand\BrandUpdateData;
use App\Modules\Catalog\Application\DTOs\Brand\BrandViewData;
use App\Modules\Catalog\Application\DTOs\Brand\FilterBrandIndexData;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{

    public function __construct(
        private readonly ListBrandUseCase $listBrandUseCase,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly ListProductByBrandUseCase $listProductByBrandUseCase,
        private readonly IndexBrandQuery $indexBrandQuery,
        private readonly CreateBrandUseCase $createBrandUseCase,
        private readonly ViewBrandQuery $viewBrandQuery,
        private readonly UpdateBrandUseCase $updateBrandUseCase,
        private readonly RemoveBrandUseCase $removeBrandUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        $filters = FilterBrandIndexData::validateAndCreate($request->all());
        $brands = $this->indexBrandQuery->execute($filters, $userPermission);
        return Inertia::render('Catalog/Brand/Index', [
            'brands' => $brands,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = BrandCreateData::validateAndCreate($request->all());

        $brand = $this->createBrandUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.brand.show', $brand->id)->with('success', 'Бренд создан');
    }

    public function show(int $id, UserPermission $userPermission): \Inertia\Response
    {
        $brand = $this->viewBrandQuery->execute($id, $userPermission);
        $dto = new ContentBlockContainerData($brand->id, ContainerType::BRAND);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Catalog/Brand/Show', [
            'brand' => BrandViewData::fromEntity($brand),
            'blocks' => $blocks,
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = BrandUpdateData::validateAndCreate($request->all());

        $brand = $this->updateBrandUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.catalog.brand.show', $brand->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        try {
            $this->removeBrandUseCase->execute($id, $userPermission);
            return redirect()->back()->with('success', 'Бренд удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        $list = $this->listBrandUseCase->execute();
        return response()->json($list);
    }

    /**
     * Список товаров бренда (с пагинацией).
     * GET /admin/catalog/brand/{id}/products
     */
    public function brandProducts(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);

        $list = $this->listProductByBrandUseCase->execute($id, $perPage, $page);

        return response()->json($list);
    }
}
