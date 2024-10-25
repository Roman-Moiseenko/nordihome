<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Service\DistributorService;
use App\Modules\Accounting\Service\SupplyService;
use App\UseCase\PaginationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DistributorController extends Controller
{
    private DistributorService $service;

    public function __construct(DistributorService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Distributor::orderBy('name');
        $distributors = $this->pagination($query, $request, $pagination);
        return view('admin.accounting.distributor.index', compact('distributors', 'pagination'));
    }

    public function create(Request $request)
    {
        $currencies = Currency::get();
        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.distributor.create', compact('currencies', 'organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'currency_id' => 'required',
            'organization_id' => 'required_without:inn',
            'inn' => 'required_without:organization_id',
        ]);
        $distributor = $this->service->create($request);
        return redirect()->route('admin.accounting.distributor.show', $distributor);

    }

    public function show(Distributor $distributor, Request $request)
    {
        //TODO Переделать под SeDeSys
        $query = DistributorProduct::where('distributor_id', $distributor->id);

        $d_products = DistributorProduct::where('distributor_id', $distributor->id)->getModels();

        $min_ids = [];
        $empty_ids = [];
        $no_buy_ids = [];

        foreach ($d_products as $d_product) {
            $sell = $d_product->product->getQuantity();
            if ($sell == 0) $empty_ids[] = $d_product->product->id;
            if ($d_product->product->isBalance()) $min_ids[] = $d_product->product->id;
            if (!$d_product->product->balance->buy) $no_buy_ids[] = $d_product->product->id;
        }

        if (!empty($balance = $request->string('balance')->value())) {
            if ($balance == 'min')
                $query->whereIn('product_id', $min_ids);
            if ($balance == 'empty')
                $query->whereIn('product_id', $empty_ids);
            if ($balance == 'no_buy')
                $query->whereIn('product_id', $no_buy_ids);
        }
        $items = $this->pagination($query, $request, $pagination);

        $count = [
            'all' => DistributorProduct::where('distributor_id', $distributor->id)->count(),
            'min' => count($min_ids),
            'empty' => count($empty_ids),
            'no_buy' => count($no_buy_ids),
        ];
        return view('admin.accounting.distributor.show', compact('distributor', 'items', 'pagination', 'count'));
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
}
