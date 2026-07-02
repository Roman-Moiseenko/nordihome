<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Catalog\Application\Actions\Brand\ListBrandUseCase;
use App\Modules\Parser\Service\ParserAbstract;
use App\Modules\Catalog\Entity\Brand;
use App\Modules\Catalog\Repository\BrandRepository;
use App\Modules\Catalog\Service\BrandService;
use App\UseCase\PaginationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class BrandController extends Controller
{

    public function __construct(
        private readonly BrandService     $service,
        private readonly BrandRepository  $repository,
        private readonly ListBrandUseCase $listBrandUseCase)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        $brands = $this->repository->getIndex($request, $filters);
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
        return Inertia::render('Catalog/Brand/Show', [
            'brand' => $this->repository->BrandWithToArray($brand, $request),
            'parsers' => array_select(ParserAbstract::PARSERS),
            'currencies' => Currency::getModels(),
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
}
