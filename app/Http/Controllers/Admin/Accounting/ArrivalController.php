<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
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
        try {
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

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $arrivals = $query->paginate($pagination);
                $arrivals->appends(['p' => $pagination]);
            } else {
                $arrivals = $query->paginate($this->pagination);
            }
            return view('admin.accounting.arrival.index',
                compact('arrivals', 'pagination', 'completed', 'storages', 'distributors', 'storage_id', 'distributor_id'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            $distributors = Distributor::get();
            $currencies = Currency::get();
            $storages = Storage::get();
            return view('admin.accounting.arrival.create', compact('distributors', 'currencies', 'storages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'distributor' => 'required',
            'storage' => 'required',
            'currency' => 'required',
        ]);
        try {
            $arrival = $this->service->create($request);
            return redirect()->route('admin.accounting.arrival.show', $arrival);

        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(ArrivalDocument $arrival)
    {
        try {
            $info = $arrival->getInfoData();
            return view('admin.accounting.arrival.show', compact('arrival', 'info'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(ArrivalDocument $arrival)
    {
        try {
            $distributors = Distributor::get();
            $currencies = Currency::get();
            $storages = Storage::get();
            return view('admin.accounting.arrival.edit', compact('arrival'), compact('distributors', 'currencies', 'storages'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, ArrivalDocument $arrival)
    {
        try {
            $request->validate([
                'distributor' => 'required',
                'storage' => 'required',
                'currency' => 'required',
            ]);
            $arrival = $this->service->update($request, $arrival);
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(ArrivalDocument $arrival)
    {
        try {
            $this->service->destroy($arrival);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function add(Request $request, ArrivalDocument $arrival)
    {
        try {
            $this->service->add($request, $arrival);
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function remove_item(ArrivalProduct $item)
    {
        try {
            $arrival = $item->document;
            $item->delete();
            return redirect()->route('admin.accounting.arrival.show', $arrival);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function completed(ArrivalDocument $arrival)
    {
        try {
            $this->service->completed($arrival);
            return redirect()->route('admin.accounting.arrival.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
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
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
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
            event(new ThrowableHasAppeared($e));
        }
        return \response()->json($result);
    }
}
