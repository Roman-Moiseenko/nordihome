<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Service\DistributorService;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
        return view('admin.accounting.distributor.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $distributor = $this->service->create($request);
        return redirect()->route('admin.accounting.distributor.show', $distributor);
    }

    public function show(Distributor $distributor, Request $request)
    {
        $query = DistributorProduct::where('distributor_id', $distributor->id);
        $items = $this->pagination($query, $request, $pagination);
        return view('admin.accounting.distributor.show', compact('distributor', 'items', 'pagination'));
    }

    public function edit(Distributor $distributor)
    {
        $currencies = Currency::get();
        return view('admin.accounting.distributor.edit', compact('distributor', 'currencies'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $distributor = $this->service->update($request, $distributor);
        return redirect()->route('admin.accounting.distributor.show', $distributor);
    }

    public function destroy(Distributor $distributor)
    {
        $this->service->destroy($distributor);
        return redirect()->back();
    }
}
