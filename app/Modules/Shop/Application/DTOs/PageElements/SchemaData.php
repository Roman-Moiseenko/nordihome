<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use JsonSerializable;

class SchemaData implements JsonSerializable
{

    //MAINDO Сделать схему для разных типов страниц
    // Основной шаблон, и сущности в массив данных
    public function __construct(
        public string $context = 'https://schema.org',
        public string $type = 'ItemList',
      // @var SchemaItem[]
        //public array $items = [],
        // ... другие свойства по необходимости
    ) {}

    public function jsonSerialize(): mixed
    {
        $data = [
            '@context' => $this->context,
            '@type'    => $this->type,
         //   'itemListElement' => array_map(fn(SchemaItem $item) => $item->toArray(), $this->items),
        ];
        return array_filter($data); // убрать пустые
    }
}
