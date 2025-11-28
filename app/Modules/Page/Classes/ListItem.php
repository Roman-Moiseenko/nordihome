<?php

namespace App\Modules\Page\Classes;

class ListItem
{
    public string $value;
    public string $label;

    public static function fromArray(array $item): static
    {
        $_item = new static();
        $_item->value = $item['value'];
        $_item->label = $item['label'];
        return $_item;
    }
}
