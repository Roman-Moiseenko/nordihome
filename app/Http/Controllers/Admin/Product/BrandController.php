<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Repository\BrandRepository;
use App\Modules\Product\Service\BrandService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class BrandController extends Controller
{
    private BrandService $service;
    private BrandRepository $repository;
    private mixed $pagination;


    public function __construct(BrandService $service, BrandRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->repository = $repository;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        try {
            $pagination = $request['p'] ?? $this->pagination;
            $brands = $this->repository->getIndex($pagination);
            if (isset($request['p'])) {
                $brands->appends(['p' => $pagination]);
            }
            return view('admin.product.brand.index', compact('brands', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create()
    {
        try {
            return view('admin.product.brand.create');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        try {
            $brand = $this->service->register($request);
            return redirect()->route('admin.product.brand.show', compact('brand'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Brand $brand)
    {
        try {
            return view('admin.product.brand.show', compact('brand'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Brand $brand)
    {
        try {
            return view('admin.product.brand.edit', compact('brand'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Brand $brand)
    {
        try {
            $brand = $this->service->update($request, $brand);
            return redirect()->route('admin.product.brand.show', compact('brand'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Brand $brand)
    {
        try {
            $this->service->delete($brand);
            return redirect()->route('admin.product.brand.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();

    }
}
