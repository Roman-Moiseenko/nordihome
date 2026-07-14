<?php

namespace App\Modules\Shop\Domain\Schema;

class WebPageSchema implements SchemaElement
{
    public function __construct(
        private string $name,
        private string $description,
        private string $url,
    ) {}

    public function toArray(): array
    {
        return [
            '@type'        => 'WebPage',
            'name'         => $this->name,
            'description'  => $this->description,
            'url'          => $this->url,
        ];
    }
}
