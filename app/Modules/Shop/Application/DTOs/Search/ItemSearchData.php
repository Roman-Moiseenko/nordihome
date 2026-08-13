<?php

namespace App\Modules\Shop\Application\DTOs\Search;

class ItemSearchData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public ?string $code = null,
        public ?string $image = null,
        public ?float $price = null,
    )
    {
    }

    public static function fromArray(array $item): self
    {
        return new self(
            id: $item['id'],
            name: $item['name'],
            url: $item['url'],
            code: $item['code'] ?? null,
            image: $item['image'] ?? null,
            price: $item['price'] ?? null,

        );
    }
}
