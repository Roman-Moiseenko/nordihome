<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Repository\DistributorRepository;
use App\Modules\Accounting\Service\DistributorService;
use App\Modules\Accounting\Service\SupplyService;
use App\Modules\Product\Entity\Product;
use App\UseCase\PaginationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DistributorController extends Controller
{
    private DistributorService $service;
    private DistributorRepository $repository;

    public function __construct(DistributorService $service, DistributorRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $distributors = $this->repository->getIndex($request, $filters);
        $currencies = Currency::orderBy('name')->getModels();
        return Inertia::render('Accounting/Distributor/Index', [
            'filters' => $filters,
            'distributors' => $distributors,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'currency' => 'required',
            'inn' => 'required',
        ]);
        try {
            $distributor = $this->service->create($request);
            return redirect()->route('admin.accounting.distributor.show', $distributor)->with('success', 'Поставщик добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Distributor $distributor, Request $request): \Inertia\Response
    {
        $query = DistributorProduct::where('distributor_id', $distributor->id);

        $min_ids = [];
        $empty_ids = [];
        $no_buy_ids = [];

        $array_products = DistributorProduct::where('distributor_id', $distributor->id)
            ->with(['product' => function ($query) {
                $query->with('balance')
                    ->withSum('storageItems as storage_quantity', 'quantity');
            }])->get(['product_id'])->toArray();

        foreach ($array_products as $item) {
            if ((int)$item['product']['storage_quantity'] == 0) $empty_ids[] = $item['product_id'];
            if ((int)$item['product']['storage_quantity'] < (int)$item['product']['balance']['min']) $min_ids[] = $item['product_id'];
            if (!$item['product']['balance']['buy']) $no_buy_ids[] = $item['product_id'];
        }

        if (!empty($balance = $request->string('balance')->value())) {
            if ($balance == 'min')
                $query->whereIn('product_id', $min_ids);
            if ($balance == 'empty')
                $query->whereIn('product_id', $empty_ids);
            if ($balance == 'no_buy')
                $query->whereIn('product_id', $no_buy_ids);
        }
        $items = $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(DistributorProduct $product) => $this->repository->ProductToArray($product));

        $count = [
            'all' => count($array_products),
            'min' => count($min_ids),
            'empty' => count($empty_ids),
            'no_buy' => count($no_buy_ids),
        ];
        //return view('admin.accounting.distributor.show', compact('distributor', 'items', 'pagination', 'count'));
        //Для Organization сделать поле Ликвидирован!!! И ставить в фильтр scopedActual()
      //  $organizations = Organization::orderBy('short_name')->active()->getModels();

        return Inertia::render('Accounting/Distributor/Show', [
            'distributor' => $this->repository->DistributorWithToArray($distributor, $request),
           // 'organizations' => $organizations,
            'products' => $items,
            'count' => $count,
        ]);

    }

    public function supply(Request $request, Distributor $distributor): RedirectResponse
    {
        try {
            $supply = $this->service->create_supply(
                $distributor,
                $request->string('balance')->value()
            );
            return redirect()->route('admin.accounting.supply.show', $supply)->with('error', 'Заказ создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function attach(Request $request, Distributor $distributor): RedirectResponse
    {
        try {
            $this->service->attach($distributor, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация добавлена к поставщику');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function detach(Request $request, Distributor $distributor): RedirectResponse
    {
        try {
            $this->service->detach($distributor, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация отсоединена от поставщика');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function default(Request $request, Distributor $distributor): RedirectResponse
    {
        try {
            $this->service->default($distributor, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация выбрана по умолчанию');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(Request $request, Distributor $distributor): RedirectResponse
    {
        try {
            $this->service->setInfo($distributor, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
