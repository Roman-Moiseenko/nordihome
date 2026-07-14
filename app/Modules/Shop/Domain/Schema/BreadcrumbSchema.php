<?php

namespace App\Modules\Shop\Domain\Schema;

class BreadcrumbSchema implements SchemaElement
{
    /** @param array{name:string, url:string}[] $items */
    public function __construct(private array $items) {}

    public function toArray(): array
    {
        if (empty($this->items)) {
            return [];
        }

        $listItems = [];
        foreach ($this->items as $index => $item) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['name'],
                'item'     => $item['url'],
            ];
        }

        return [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
