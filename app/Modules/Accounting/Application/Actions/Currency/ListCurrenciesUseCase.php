<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Currency;

use App\Modules\Accounting\Application\DTOs\CurrencyListData;
use App\Modules\Accounting\Entity\Currency;

readonly class ListCurrenciesUseCase
{
    /**
     * @return CurrencyListData[]
     */
    public function execute(): array
    {
        return array_map(fn(Currency $currency) => new CurrencyListData(
            id: $currency->id,
            name: $currency->name,
            sign: $currency->sign,
        ), Currency::orderBy('name')->getModels());
    }
}
