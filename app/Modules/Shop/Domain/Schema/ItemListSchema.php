<?php

namespace App\Modules\Shop\Domain\Schema;

class ItemListSchema implements SchemaElement
{
    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(private array $items) {}

    public function toArray(): array
    {
        $listItems = [];

        foreach ($this->items as $index => $item) {
            $entry = [];

            foreach ($item as $key => $value) {
                // Ключ "type" считаем синонимом "@type".
                if ($key === 'type') {
                    $entry['@type'] = $value;
                    continue;
                }

                $entry[$key] = $value;
            }

            $entry['@type'] ??= 'ListItem';
            $entry['position'] = $index + 1;

            $listItems[] = $entry;
        }

        return [
            '@type'           => 'ItemList',
            'itemListElement' => $listItems,
        ];
    }
}
