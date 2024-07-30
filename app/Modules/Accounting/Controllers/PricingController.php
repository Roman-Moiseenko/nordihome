<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;

class PricingController extends Controller
{

    private PricingService $service;
    private ProductRepository $products;

    public function __construct(PricingService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:pricing']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return view('admin.accounting.pricing.index');
    }

    public function create(Request $request)
    {
        $pricing = $this->service->create();
        return redirect()->route('admin.accounting.pricing.show', $pricing); //view('admin.accounting.pricing.create');
    }

    public function create_arrival(ArrivalDocument $arrival)
    {
        $pricing = $this->service->create_arrival($arrival);
        return redirect()->route('admin.accounting.pricing.show', $pricing); //view('admin.accounting.pricing.create');
    }

    public function show(PricingDocument $pricing)
    {
        return view('admin.accounting.pricing.show', compact('pricing'));
    }

    public function destroy(PricingDocument $pricing)
    {
        $this->service->destroy($pricing);
        return redirect()->back();
    }

    public function completed(PricingDocument $pricing)
    {
        $this->service->completed($pricing);
        return redirect()->route('admin.accounting.pricing.index');
    }

    public function remove_item(PricingProduct $item)
    {
        $this->service->remove_item($item);
        return redirect()->back();
    }

    public function add(Request $request, PricingDocument $pricing)
    {
        $this->service->add($pricing, (int)$request['product_id']);
        return redirect()->route('admin.accounting.pricing.show', $pricing);
    }

    public function add_products(Request $request, PricingDocument $pricing)
    {
        $this->service->add_products($pricing, $request['products']);
        return redirect()->route('admin.accounting.pricing.show', $pricing);
    }

    public function set(PricingProduct $item, Request $request)
    {
        $this->service->set($item, $request->all());
        return response()->json(true);
    }

}
