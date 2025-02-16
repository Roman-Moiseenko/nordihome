<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Service\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    private CurrencyService $service;

    public function __construct(CurrencyService $service)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $currencies = Currency::orderBy('name')->get()->toArray();
        return Inertia::render('Accounting/Currency/Index', [
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $currency = $this->service->create($request);
        return redirect()->route('admin.accounting.currency.show', $currency)->with('success', 'Валюта добавлена');
    }

    public function show(Currency $currency): Response
    {
        return Inertia::render('Accounting/Currency/Show', [
            'currency' => $currency,
        ]);
    }

    public function update(Request $request, Currency $currency): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'exchange' => 'required|numeric'
        ]);
        $this->service->update($request, $currency);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        $this->service->destroy($currency);
        return redirect()->back()->with('success', 'Валюта удалена');
    }
}
