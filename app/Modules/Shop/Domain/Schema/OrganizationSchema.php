<?php

namespace App\Modules\Shop\Domain\Schema;

class OrganizationSchema implements SchemaElement
{
    public function __construct(
        private string $name,
        private string $url,
        private string $logoUrl = '',
        /** @var string[] $sameAs */ private array $sameAs = []
    ) {}

    public function toArray(): array
    {
        return array_filter([
            '@type'  => 'Organization',
            'name'   => $this->name,
            'url'    => $this->url,
            'logo'   => $this->logoUrl ?: null,
            'sameAs' => $this->sameAs ?: null,
        ]);
    }
}
