<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Repository\BrandRepository;
use App\Modules\Product\Service\BrandService;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = $this->repository->getIndex();
            $brands = $this->pagination($query, $request, $pagination);
            return view('admin.product.brand.index', compact('brands', 'pagination'));
        });
    }

    public function create()
    {
        return view('admin.product.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        return $this->try_catch_admin(function () use($request) {
            $brand = $this->service->register($request);
            return redirect()->route('admin.product.brand.show', compact('brand'));
        });
    }

    public function show(Brand $brand)
    {
        return $this->try_catch_admin(function () use($brand) {
            return view('admin.product.brand.show', compact('brand'));
        });
    }

    public function edit(Brand $brand)
    {
        return $this->try_catch_admin(function () use($brand) {
            return view('admin.product.brand.edit', compact('brand'));
        });
    }

    public function update(Request $request, Brand $brand)
    {
        return $this->try_catch_admin(function () use($request, $brand) {
            $brand = $this->service->update($request, $brand);
            return redirect()->route('admin.product.brand.show', compact('brand'));
        });
    }

    public function destroy(Brand $brand)
    {
        return $this->try_catch_admin(function () use($brand) {
            $this->service->delete($brand);
            return redirect()->route('admin.product.brand.index');
        });
    }
}
