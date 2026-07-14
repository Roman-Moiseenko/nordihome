<?php

namespace App\Modules\Shop\Domain\Schema;

class ProductSchema implements SchemaElement
{
    public function __construct(
        private string $name,
        private string $description,
        private string $image,
        private string $sku,
        private ?OfferSchema $offer = null,
        private string $url = '',
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type'        => 'Product',
            'name'         => $this->name,
            'description'  => $this->description,
            'image'        => $this->image,
            'sku'          => $this->sku,
            'url'          => $this->url,
        ];

        if ($this->offer) {
            $data['offers'] = $this->offer->toArray();
        }

        return $data;
    }
}
