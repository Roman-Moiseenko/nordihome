<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Currency;
use Illuminate\Http\Request;

class CurrencyService
{

    public function create(Request $request)
    {
        return Currency::register(
            $request->string('name')->trim()->value(),
            $request->string('sign')->trim()->value(),
           $request->float('exchange'),
        );
    }

    public function update(Request $request, Currency $currency): Currency
    {
        $currency->name = $request->string('name')->trim()->value();
        $currency->sign = $request->string('sign')->trim()->value();
        $currency->exchange = $request->float('exchange');
        $currency->save();
        return $currency;
    }

    public function destroy(Currency $currency)
    {
        if (!empty($currency->arrivals)) throw new \DomainException('Имеются документы, удалить нельзя');
        $currency->delete();
    }
}
