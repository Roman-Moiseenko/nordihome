<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
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
        try {
            $currencies = Currency::orderBy('name')->get();
            return view('admin.accounting.currency.index', compact('currencies'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            return view('admin.accounting.currency.create');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);
            $currency = $this->service->create($request);
            return redirect()->route('admin.accounting.currency.show', $currency);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Currency $currency)
    {
        try {
            return view('admin.accounting.currency.show', compact('currency'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Currency $currency)
    {
        try {
            return view('admin.accounting.currency.edit', compact('currency'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Currency $currency)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $currency = $this->service->update($request, $currency);
            return redirect()->route('admin.accounting.currency.show', $currency);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Currency $currency)
    {
        try {
            $this->service->destroy($currency);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
