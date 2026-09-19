<?php

namespace App\Modules\Storefront\Application\Services;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Storefront\Application\DTOs\ClientContext;

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
            ip: request()->ip(),
            region: request()->cookie('user_cookie_region')
        );
    }
}
