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
/*
    public function create(Request $request)
    {
        $currencies = Currency::get();
        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.distributor.create', compact('currencies', 'organizations'));
    }
*/
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'currency' => 'required',
            'inn' => 'required',
        ]);
        $distributor = $this->service->create($request);
        return redirect()->route('admin.accounting.distributor.show', $distributor);

    }

    public function show(Distributor $distributor, Request $request): \Inertia\Response
    {
        $query = DistributorProduct::where('distributor_id', $distributor->id);
        $d_products = DistributorProduct::where('distributor_id', $distributor->id)->getModels();

        $min_ids = [];
        $empty_ids = [];
        $no_buy_ids = [];

        foreach ($d_products as $d_product) {
            $sell = $d_product->product->getQuantity();
            if ($sell == 0) $empty_ids[] = $d_product->product_id;
            if ($d_product->product->isBalance()) $min_ids[] = $d_product->product_id;
            if (!$d_product->product->balance->buy) $no_buy_ids[] = $d_product->product_id;
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
            'all' => count($d_products), //DistributorProduct::where('distributor_id', $distributor->id)->count(),
            'min' => count($min_ids),
            'empty' => count($empty_ids),
            'no_buy' => count($no_buy_ids),
        ];
        //return view('admin.accounting.distributor.show', compact('distributor', 'items', 'pagination', 'count'));
        //Для Organization сделать поле Ликвидирован!!! И ставить в фильтр scopedActual()
        $organizations = Organization::orderBy('short_name')->getModels();

        return Inertia::render('Accounting/Distributor/Show', [
            'distributor' => $this->repository->DistributorWithToArray($distributor, $request),
            'organizations' => $organizations,
            'products' => $items,
            'count' => $count,
        ]);

    }

    public function edit(Distributor $distributor): View
    {
        $currencies = Currency::get();
        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.distributor.edit', compact('distributor', 'currencies', 'organizations'));
    }

    public function update(Request $request, Distributor $distributor): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);
        $distributor = $this->service->update($request, $distributor);
        return redirect()->route('admin.accounting.distributor.show', $distributor);
    }

    public function destroy(Distributor $distributor): RedirectResponse
    {
        $this->service->destroy($distributor);
        return redirect()->back();
    }

    public function supply(Request $request, Distributor $distributor): RedirectResponse
    {
        $supply = $this->service->create_supply(
            $distributor,
            $request->string('balance')->value()
        );
        return redirect()->route('admin.accounting.supply.show', $supply);
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


}
