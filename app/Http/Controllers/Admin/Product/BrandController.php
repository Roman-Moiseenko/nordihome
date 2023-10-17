<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Service\BrandService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BrandController extends Controller
{
    private BrandService $service;

    public function __construct(BrandService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
    }

    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(20);
        return view('admin.product.brand.index', compact('brands'));
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
        $brand = $this->service->register($request);
        return redirect()->route('admin.product.brand.show', compact('brand'));
    }

    public function show(Brand $brand)
    {
        return view('admin.product.brand.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.product.brand.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $brand = $this->service->update($request, $brand);
        return view('admin.product.brand.show', compact('brand'));
    }

    public function destroy(Brand $brand)
    {
        try {
            $this->service->delete($brand);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect('admin/product/brand');
    }
}
