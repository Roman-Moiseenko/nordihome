<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Repository\TraderRepository;
use App\Modules\Accounting\Service\TraderService;
use App\Modules\Guide\Entity\VAT;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class TraderController extends Controller
{
    private TraderService $service;
    private TraderRepository $repository;

    public function __construct(TraderService $service, TraderRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $traders = Trader::orderBy('name')->with('organization')->get();
        return Inertia::render('Accounting/Trader/Index', [
            'traders' => $traders,
        ]);
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
       // $organizations = Organization::orderBy('short_name')->active()->getModels();
        return Inertia::render('Accounting/Trader/Show', [
            'trader' => $this->repository->TraderWithToArray($trader),
           // 'organizations' => $organizations,
        ]);
    }

    public function destroy(Trader $trader): RedirectResponse
    {
        $this->service->destroy($trader);
        return redirect()->back();
    }

    public function attach(Request $request, Trader $trader): RedirectResponse
    {
        try {
            $this->service->attach($trader, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация добавлена к продавцу');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function detach(Request $request, Trader $trader): RedirectResponse
    {
        try {
            $this->service->detach($trader, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация отсоединена от продавца');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function default(Request $request, Trader $trader): RedirectResponse
    {
        try {
            $this->service->default($trader, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация выбрана по умолчанию');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(Request $request, Trader $trader): RedirectResponse
    {
        try {
            $this->service->setInfo($trader, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
