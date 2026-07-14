<?php

namespace App\Modules\Shop\Domain\Schema;

use JsonSerializable;

class SchemaData implements JsonSerializable
{
    public string $context;
    /** @var SchemaElement[] */
    public array $graph;
    //MAINDO Сделать схему для разных типов страниц
    // Основной шаблон, и сущности в массив данных
    public function __construct(array $graph = [])
    {
        $this->context = 'https://schema.org';
        $this->graph = $graph;
    }

    public function jsonSerialize(): array
    {
        return [
            '@context' => $this->context,
            '@graph'   => array_map(fn(SchemaElement $el) => $el->toArray(), $this->graph),
        ];
    }
}
