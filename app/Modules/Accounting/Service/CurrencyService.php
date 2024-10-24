<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Setting;
use Illuminate\Http\Request;

class CurrencyService
{

    public function create(Request $request): Currency
    {
        $currency = Currency::register(
            $request->string('name')->trim()->value(),
            $request->string('sign')->trim()->value(),
            $request->float('exchange'),
            $request->string('cbr_code')->trim()->value(),
            $request->integer('extra'),
        );
        $this->update_parser_currency($currency);
        return $currency;
    }

    public function update(Request $request, Currency $currency): Currency
    {
        $currency->name = $request->string('name')->trim()->value();
        $currency->sign = $request->string('sign')->trim()->value();
        $currency->exchange = $request->float('exchange');
        $currency->cbr_code = $request->string('cbr_code')->trim()->value();
        $currency->extra = $request->integer('extra');
        $currency->save();
        $this->update_parser_currency($currency);
        return $currency;
    }

    public function destroy(Currency $currency): void
    {
        if (!empty($currency->arrivals)) throw new \DomainException('Имеются документы, удалить нельзя');
        $currency->delete();
    }

    private function update_parser_currency(Currency $currency): void
    {
        if ($currency->cbr_code == 'PLN') {
            $setting = Setting::where('slug', 'parser')->first();
            $parser = new Parser($setting->getData());
            $parser->parser_coefficient = $currency->getExchange();
            $setting->setData($parser);
        }
    }
}
