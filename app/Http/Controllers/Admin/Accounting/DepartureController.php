<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\DepartureService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use function Symfony\Component\Translation\t;

class DepartureController extends Controller
{
    private DepartureService $service;
    private ProductRepository $products;


    public function __construct(DepartureService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = DepartureDocument::orderByDesc('created_at');
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';
            if ($completed == 'active') $query->where('completed', '=', true);
            if ($completed == 'draft') $query->where('completed', '=', false);

            if (!empty($storage_id = $request->get('storage_id'))) {
                $query->where('storage_id', $storage_id);
            }

            $departures = $this->pagination($query, $request, $pagination);
            return view('admin.accounting.departure.index',
                compact('departures', 'pagination', 'completed', 'storages', 'storage_id'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $departure = $this->service->create((int)$request['storage']);
            return redirect()->route('admin.accounting.departure.show', $departure);
        });
    }

    public function show(DepartureDocument $departure)
    {
        return $this->try_catch_admin(function () use($departure) {
            $info = $departure->getInfoData();
            return view('admin.accounting.departure.show', compact('departure', 'info'));
        });
    }

    public function destroy(DepartureDocument $departure)
    {
        return $this->try_catch_admin(function () use($departure) {
            $this->service->destroy($departure);
            return redirect()->back();
        });
    }

    public function add(Request $request, DepartureDocument $departure)
    {
        return $this->try_catch_admin(function () use($request, $departure) {
            $this->service->add($departure, (int)$request['product_id'], (int)$request['quantity']);
            return redirect()->route('admin.accounting.departure.show', $departure);
        });
    }

    public function add_products(Request $request, DepartureDocument $departure)
    {
        return $this->try_catch_admin(function () use($request, $departure) {
            $this->service->add_products($departure, $request['products']);
            return redirect()->route('admin.accounting.departure.show', $departure);
        });
    }

    public function remove_item(DepartureProduct $item)
    {
        return $this->try_catch_admin(function () use($item) {
            $movement = $item->document;
            $item->delete();
            return redirect()->route('admin.accounting.departure.show', $movement);
        });
    }

    public function completed(DepartureDocument $departure)
    {
        return $this->try_catch_admin(function () use($departure) {
            $this->service->completed($departure);
            return redirect()->route('admin.accounting.departure.index');
        });
    }

    //AJAX
    public function set(Request $request, DepartureProduct $item)
    {
        return $this->try_catch_ajax_admin(function () use($request, $item) {
            $result = $this->service->set($request, $item);
            return response()->json($result);
        });
    }

    public function search(Request $request, DepartureDocument $departure)
    {
        return $this->try_catch_ajax_admin(function () use($request, $departure) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$departure->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }

}
