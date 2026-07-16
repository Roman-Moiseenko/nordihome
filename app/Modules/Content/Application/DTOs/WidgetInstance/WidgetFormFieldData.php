<?php

namespace App\Modules\Content\Application\DTOs\WidgetInstance;

use Spatie\LaravelData\Data;

class WidgetFormFieldData extends Data
{
    public function __construct(
        public readonly string $name,           // ключ в params
        public readonly string $type,           // string | integer | boolean | array | object
        public readonly string $label,          // из title схемы
        public readonly mixed $value,           // текущее значение из params экземпляра
        public readonly mixed $default = null,  // значение по умолчанию из схемы
        public readonly bool $required = false, // обязательное поле?
        public readonly ?string $format = null, // color | widget | html | uri | ...
        public readonly ?array $options = null, // для enum-полей
        /** @var WidgetFormFieldData[]|null */
        public readonly ?array $nestedFields = null, // для type === 'object' — вложенные поля
    ) {}
}
