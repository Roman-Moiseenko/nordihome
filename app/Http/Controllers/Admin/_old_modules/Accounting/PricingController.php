<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Accounting;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use function redirect;
use function response;
use function view;

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
        return $this->try_catch_admin(function () use($request) {
           /* $query = PricingDocument::orderByDesc('created_at');
            $completed = $request['completed'] ?? 'all';
            if ($completed == 'active') $query->where('completed', '=', true);
            if ($completed == 'draft') $query->where('completed', '=', false);
            $pricing_documents = $this->pagination($query, $request, $pagination); */
            return view('admin.accounting.pricing.index'/*,
                compact('pricing_documents', 'pagination', 'completed')*/);
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $pricing = $this->service->create();
            return redirect()->route('admin.accounting.pricing.show', $pricing); //view('admin.accounting.pricing.create');
        });
    }

    public function create_arrival(ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($arrival) {
            $pricing = $this->service->create_arrival($arrival);
            return redirect()->route('admin.accounting.pricing.show', $pricing); //view('admin.accounting.pricing.create');
        });
    }

    public function show(PricingDocument $pricing)
    {
        return $this->try_catch_admin(function () use($pricing) {
            return view('admin.accounting.pricing.show', compact('pricing'));
        });
    }

    public function destroy(PricingDocument $pricing)
    {
        return $this->try_catch_admin(function () use($pricing) {
            $this->service->destroy($pricing);
            return redirect()->back();
        });
    }

    public function completed(PricingDocument $pricing)
    {
        return $this->try_catch_admin(function () use($pricing) {
            $this->service->completed($pricing);
            return redirect()->route('admin.accounting.pricing.index');
        });
    }

    public function remove_item(PricingProduct $item)
    {
        return $this->try_catch_admin(function () use($item) {
            $this->service->remove_item($item);
            return redirect()->back();
        });
    }

    public function add(Request $request, PricingDocument $pricing)
    {
        return $this->try_catch_admin(function () use($request, $pricing) {
            $this->service->add($pricing, (int)$request['product_id']);
            return redirect()->route('admin.accounting.pricing.show', $pricing);
        });
    }

    public function add_products(Request $request, PricingDocument $pricing)
    {
        return $this->try_catch_admin(function () use($request, $pricing) {
            $this->service->add_products($pricing, $request['products']);
            return redirect()->route('admin.accounting.pricing.show', $pricing);
        });
    }

    public function set(PricingProduct $item, Request $request) {
        return $this->try_catch_ajax_admin(function () use($request, $item) {
            $this->service->set($item, $request->all());
            return response()->json(true);
        });
    }


    public function search(Request $request, PricingDocument $pricing)
    {
        return $this->try_catch_ajax_admin(function () use($request, $pricing) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$pricing->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }

}
