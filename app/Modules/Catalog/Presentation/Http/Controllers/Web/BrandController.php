<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Catalog\Application\Actions\Brand\IndexBrandQuery;
use App\Modules\Catalog\Application\Actions\Brand\ListBrandUseCase;
use App\Modules\Catalog\Application\Actions\Product\ListProductByBrandUseCase;
use App\Modules\Catalog\Application\DTOs\Brand\FilterBrandIndexData;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Repository\BrandRepository;
use App\Modules\Catalog\Service\BrandService;
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
        private readonly BrandService     $service,
        private readonly BrandRepository  $repository,
        private readonly ListBrandUseCase $listBrandUseCase,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly ListProductByBrandUseCase $listProductByBrandUseCase,
        private readonly IndexBrandQuery $indexBrandQuery,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission): \Inertia\Response
    {
        $filters = FilterBrandIndexData::validateAndCreate($request->all());
        $brands = $this->indexBrandQuery->execute($filters, $userPermission); // $this->repository->getIndex($request, $filters);
        return Inertia::render('Catalog/Brand/Index', [
            'brands' => $brands,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        try {
            $brand = $this->service->create($request);
            return redirect()->route('admin.catalog.brand.show', $brand)->with('success', 'Бренд создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Brand $brand, Request $request): \Inertia\Response
    {
        $dto = new ContentBlockContainerData($brand->id, ContainerType::BRAND);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Catalog/Brand/Show', [
            'brand' => $this->repository->BrandWithToArray($brand, $request),
            'currencies' => Currency::getModels(),
            'blocks' => $blocks,
        ]);
    }

    public function set_info(Request $request, Brand $brand): RedirectResponse
    {
        try {
            $this->service->setInfo($request, $brand);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        try {
            $this->service->delete($brand);
            return redirect()->back()->with('success', 'Удалено');
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
