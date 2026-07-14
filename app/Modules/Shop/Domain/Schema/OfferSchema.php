<?php

namespace App\Modules\Shop\Domain\Schema;

class OfferSchema implements SchemaElement
{
    public function __construct(
        private float $price,
        private string $priceCurrency = 'RUB',
        private string $availability = 'InStock',
    ) {}

    public function toArray(): array
    {
        return [
            '@type'         => 'Offer',
            'price'         => $this->price,
            'priceCurrency' => $this->priceCurrency,
            'availability'  => 'https://schema.org/' . $this->availability,
        ];
    }
}
