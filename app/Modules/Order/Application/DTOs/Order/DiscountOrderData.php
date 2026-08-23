<?php

namespace App\Modules\Order\Application\DTOs\Order;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

class DiscountOrderData extends Data
{
    public function __construct(
        #[Nullable, Numeric]
        public ?float $percent = null,
        #[Nullable, Numeric]
        public ?float $manual = null,
    ){}


    public function isPercent(): bool
    {
        return $this->percent !== null;
    }

    public static function rules(): array
    {
        return [
            'percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                // Если заданы и percent, и manual – ошибка
                function ($attribute, $value, $fail) {
                    if ($value !== null && request()->input('manual') !== null) {
                        $fail('Укажите либо процент, либо сумму скидки, но не оба значения.');
                    }
                },
                // percent обязателен, если не задан amount
                'required_without:manual',
            ],
            'manual' => [
                'nullable',
                'numeric',
                'min:0',
                // amount обязателен, если не задан percent
                'required_without:percent',
            ],
        ];
    }
}
