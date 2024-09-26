<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Service\TraderService;
use Illuminate\Http\Request;


class TraderController extends Controller
{
    private TraderService $service;

    public function __construct(TraderService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Trader::orderBy('name');
        $traders = $this->pagination($query, $request, $pagination);
        return view('admin.accounting.trader.index', compact('traders', 'pagination'));
    }

    public function create(Request $request)
    {

        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.trader.create', compact( 'organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'required_without:inn',
            'inn' => 'required_without:organization_id',
        ]);
        $trader = $this->service->create($request);
        return redirect()->route('admin.accounting.trader.show', $trader);

    }

    public function show(Trader $trader, Request $request)
    {
        return view('admin.accounting.trader.show', compact('trader'));
    }

    public function edit(Trader $trader)
    {
        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.trader.edit', compact('trader', 'organizations'));
    }

    public function update(Request $request,  Trader $trader)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $this->service->update($request, $trader);
        return redirect()->route('admin.accounting.trader.show', $trader);
    }

    public function destroy(Trader $trader)
    {
        $this->service->destroy($trader);
        return redirect()->back();
    }
}
