<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Service\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    private CurrencyService $service;

    public function __construct(CurrencyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $currencies = Currency::orderBy('name')->get();

        return view('admin.accounting.currency.index', compact('currencies'));
    }

    public function create(Request $request)
    {
        return view('admin.accounting.currency.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $currency = $this->service->create($request);
        return redirect()->route('admin.accounting.currency.show', $currency);
    }

    public function show(Currency $currency)
    {
        return view('admin.accounting.currency.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('admin.accounting.currency.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $currency = $this->service->update($request, $currency);
        return redirect()->route('admin.accounting.currency.show', $currency);
    }

    public function destroy(Currency $currency)
    {
        try {
            $this->service->destroy($currency);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return redirect()->back();
    }
}
