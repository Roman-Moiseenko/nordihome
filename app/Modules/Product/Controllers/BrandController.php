<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Repository\BrandRepository;
use App\Modules\Product\Service\BrandService;
use App\UseCase\PaginationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class BrandController extends Controller
{
    private BrandService $service;
    private BrandRepository $repository;


    public function __construct(BrandService $service, BrandRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $brands = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Brand/Index', [
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
            return redirect()->route('admin.product.brand.show', $brand)->with('success', 'Бренд создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Brand $brand, Request $request)
    {
        return Inertia::render('Product/Brand/Show', [
            'brand' => $this->repository->BrandWithToArray($brand, $request),
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
        $list = Brand::orderBy('name')->get()->map(function (Brand $brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
            ];
        });
        return response()->json($list);
    }
}
