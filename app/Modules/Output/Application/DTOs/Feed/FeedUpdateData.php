<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * Единый DTO обновления фида.
 *
 * Отправляется только изменяемый параметр:
 *  - скалярные поля: name, setPreprice, setTitle, setDescription;
 *  - мутация списка: field + action (add|remove|clear) + in + ids.
 */
class FeedUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name = null,
        #[Nullable, BooleanType]
        public readonly ?bool $setPreprice = null,
        #[Nullable, BooleanType]
        public readonly ?bool $active = null,
        #[Nullable, StringType]
        public readonly ?string $setTitle = null,
        #[Nullable, StringType]
        public readonly ?string $setDescription = null,
        #[Nullable]
        public readonly ?int $priceMin = null,
        #[Nullable]
        public readonly ?int $priceMax = null,
        #[Nullable, BooleanType]
        public readonly ?bool $priceChanged = null,
        #[Nullable, StringType]
        public readonly ?string $field = null,
        #[Nullable, StringType]
        public readonly ?string $action = null,
        #[Nullable, BooleanType]
        public readonly ?bool $in = null,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $ids = null,
    ) {}
}
