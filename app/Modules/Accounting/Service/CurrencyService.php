<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Currency;
use Illuminate\Http\Request;

class CurrencyService
{

    public function create(Request $request)
    {
        $currency = Currency::register(
            $request['name'],
            $request['sign'],
            (float)$request['exchange'],
        );

        return $currency;
    }

    public function update(Request $request, Currency $currency)
    {
        $currency->name = $request['name'];
        $currency->sign = $request['sign'];
        $currency->exchange = $request['exchange'];
        $currency->save();
        return $currency;
    }

    public function destroy(Currency $currency)
    {
        if (!empty($currency->arrivals)) throw new \DomainException('Имеются документы, удалить нельзя');
        $currency->delete();
    }
}
