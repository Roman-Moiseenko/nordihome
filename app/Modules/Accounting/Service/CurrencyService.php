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
            $request->string('cbr_code')->trim()->value()
        );
        $currency->code = $request->input('code');
        $currency->save();
        $this->update_parser_currency($currency);
        return $currency;
    }

    public function update(Request $request, Currency $currency): void
    {

        if ($request->float('default') && !$currency->default) {
            Currency::where('default', true)->update(['default' => false]);
            $currency->default = true;
        }
        $currency->name = $request->string('name')->trim()->value();
        $currency->sign = $request->string('sign')->trim()->value();
        $currency->exchange = $request->float('exchange');
        $currency->cbr_code = $request->string('cbr_code')->trim()->value();
        $currency->fixed = $request->float('fixed');
        $currency->code = $request->input('code');
        $currency->save();
        $this->update_parser_currency($currency);
    }

    public function destroy(Currency $currency): void
    {

        if ($currency->arrivals()->count() > 0 || $currency->supplies()->count() > 0) throw new \DomainException('Имеются документы, удалить нельзя');
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
