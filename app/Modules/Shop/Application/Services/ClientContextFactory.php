<?php

namespace App\Modules\Shop\Application\Services;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Shop\Application\DTOs\ClientContext;

class ClientContextFactory
{
    public function make(): ClientContext
    {
        $client = (auth()->check() && auth()->user()->isClient())
            ? auth()->user()->profileable
            : null;

        return new ClientContext(
            id: $client->id ?? null,
            uuid: request()->cookie('user_cookie_id'),
            priceType: $client?->getPriceType() ?? PriceType::retail(),
        );
    }
}
