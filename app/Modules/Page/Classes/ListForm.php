<?php

namespace App\Modules\Page\Classes;

class ListForm
{

    public string $slug;
    public string $name;
    /** @var string[] $items */
    public array $items;


    public static function create(mixed $slug): static
    {
        $list = new static();
        $list->slug = $slug;
        return $list;
    }


    public function add(string $item): void
    {
        $this->items[] = $item;
    }

}
