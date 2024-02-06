<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class ArrivalController extends Controller
{
    private mixed $pagination;
    private ArrivalService $service;
    private ProductRepository $products;

    public function __construct(ArrivalService $service, ProductRepository $products)
    {
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $query = ArrivalDocument::orderByDesc('created_at');

        $completed = $request['completed'] ?? 'all';
        if ($completed == 'true') $query->where('completed', '=', true);
        if ($completed == 'false') $query->where('completed', '=', false);

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $arrivals = $query->paginate($pagination);
            $arrivals->appends(['p' => $pagination]);
        } else {
            $arrivals = $query->paginate($this->pagination);
        }

        return view('admin.accounting.arrival.index', compact('arrivals', 'pagination', 'completed'));
    }

    public function create(Request $request)
    {
        $distributors = Distributor::get();
        $currencies = Currency::get();
        $storages = Storage::get();
        return view('admin.accounting.arrival.create', compact('distributors', 'currencies', 'storages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor' => 'required',
            'storage' => 'required',
            'currency' => 'required',
        ]);
        $arrival = $this->service->create($request);
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function show(ArrivalDocument $arrival)
    {
        $info = $arrival->getInfoData();
        return view('admin.accounting.arrival.show', compact('arrival', 'info'));
    }

    public function edit(ArrivalDocument $arrival)
    {
        $distributors = Distributor::get();
        $currencies = Currency::get();
        $storages = Storage::get();
        return view('admin.accounting.arrival.edit', compact('arrival'), compact('distributors', 'currencies', 'storages'));
    }

    public function update(Request $request, ArrivalDocument $arrival)
    {
        $request->validate([
            'distributor' => 'required',
            'storage' => 'required',
            'currency' => 'required',
        ]);
        $arrival = $this->service->update($request, $arrival);
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function destroy(ArrivalDocument $arrival)
    {
        try {
            $this->service->destroy($arrival);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return redirect()->back();
    }

    public function add(Request $request, ArrivalDocument $arrival)
    {
        $this->service->add($request, $arrival);

        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function remove_item(ArrivalProduct $item) {
        $arrival = $item->document;
        $item->delete();
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function completed(ArrivalDocument $arrival)
    {
        $this->service->completed($arrival);
        return redirect()->route('admin.accounting.arrival.index');
    }

    //AJAX
    public function set(Request $request, ArrivalProduct $item)
    {
        try {
            $cost_ru = $this->service->set($request, $item);
            return response()->json([
                'cost_ru' => $cost_ru,
                'info' => $item->document->getInfoData(),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function search(Request $request, ArrivalDocument $arrival)
    {
        $result = [];
        try {
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$arrival->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }
        return \response()->json($result);
    }
}
