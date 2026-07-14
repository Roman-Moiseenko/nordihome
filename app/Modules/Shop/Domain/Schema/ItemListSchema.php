<?php

namespace App\Modules\Shop\Domain\Schema;

class ItemListSchema implements SchemaElement
{
    /** @param array{name:string, url:string}[] $items */
    public function __construct(private array $items) {}

    public function toArray(): array
    {
        $listItems = [];
        foreach ($this->items as $index => $item) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'url'      => $item['url'],
                'name'     => $item['name'],
            ];
        }

        return [
            '@type'           => 'ItemList',
            'itemListElement' => $listItems,
        ];
    }
}
