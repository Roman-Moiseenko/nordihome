<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;

class ArrivalController extends Controller
{
    private ArrivalService $service;
    private ProductRepository $products;

    public function __construct(ArrivalService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = ArrivalDocument::orderByDesc('created_at');
            $distributors = Distributor::orderBy('name')->get();
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';
            if ($completed == 'true') $query->where('completed', '=', true);
            if ($completed == 'false') $query->where('completed', '=', false);
            if (!empty($distributor_id = $request->get('distributor_id'))) {
                $query->where('distributor_id', $distributor_id);
            }
            if (!empty($storage_id = $request->get('storage_id'))) {
                $query->where('storage_id', $storage_id);
            }

            $arrivals = $this->pagination($query, $request, $pagination);

            return view('admin.accounting.arrival.index',
                compact('arrivals', 'pagination', 'completed', 'storages', 'distributors', 'storage_id', 'distributor_id'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $distributors = Distributor::get();
            $currencies = Currency::get();
            $storages = Storage::get();
            return view('admin.accounting.arrival.create', compact('distributors', 'currencies', 'storages'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor' => 'required',
            'storage' => 'required',
            'currency' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $arrival = $this->service->create($request);
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        });
    }

    public function show(ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($arrival) {
            $info = $arrival->getInfoData();
            return view('admin.accounting.arrival.show', compact('arrival', 'info'));
        });
    }

    public function edit(ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($arrival) {
            $distributors = Distributor::get();
            $currencies = Currency::get();
            $storages = Storage::get();
            return view('admin.accounting.arrival.edit', compact('arrival'), compact('distributors', 'currencies', 'storages'));
        });
    }

    public function update(Request $request, ArrivalDocument $arrival)
    {
        $request->validate([
            'distributor' => 'required',
            'storage' => 'required',
            'currency' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request, $arrival) {
            $arrival = $this->service->update($request, $arrival);
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        });
    }

    public function destroy(ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($arrival) {
            $this->service->destroy($arrival);
            return redirect()->back();
        });
    }

    public function add(Request $request, ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($request, $arrival) {
            $this->service->add($request, $arrival);
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        });
    }

    public function remove_item(ArrivalProduct $item)
    {
        return $this->try_catch_admin(function () use($item) {
            $arrival = $item->document;
            $item->delete();
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        });
    }

    public function completed(ArrivalDocument $arrival)
    {
        return $this->try_catch_admin(function () use($arrival) {
            $this->service->completed($arrival);
            return redirect()->route('admin.accounting.arrival.index');
        });
    }

    //AJAX
    public function set(Request $request, ArrivalProduct $item)
    {
        return $this->try_catch_ajax_admin(function () use($request, $item) {
            $cost_ru = $this->service->set($request, $item);
            return response()->json([
                'cost_ru' => $cost_ru,
                'info' => $item->document->getInfoData(),
            ]);
        });
    }

    public function search(Request $request, ArrivalDocument $arrival)
    {
        return $this->try_catch_ajax_admin(function () use($request, $arrival) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$arrival->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }
}
