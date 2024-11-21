<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Service\TraderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class TraderController extends Controller
{
    private TraderService $service;

    public function __construct(TraderService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $traders = Trader::orderBy('name')->with('organization')->get();

        return Inertia::render('Accounting/Trader/Index', [
            'traders' => $traders,
        ]);
    }

    public function create(Request $request)
    {

        $organizations = Organization::orderBy('short_name')->where('active', true)->getModels();
        return view('admin.accounting.trader.create', compact( 'organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'inn' => 'required|string|min:10|max:12',
        ]);
        try {
            $trader = $this->service->create($request);
            return redirect()->route('admin.accounting.trader.show', $trader)->with('success', 'Продавец добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Trader $trader, Request $request): Response
    {
        //return view('admin.accounting.trader.show', compact('trader'));
        return Inertia::render('Accounting/Trader/Show', [
            'trader' => $trader,
        ]);
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
