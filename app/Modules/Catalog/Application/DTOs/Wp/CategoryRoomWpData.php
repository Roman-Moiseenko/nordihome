<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Wp;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CategoryRoomWpData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $wpId,
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, Numeric]
        public readonly ?int $parentId,
    )
    {
    }

    /**
     * Создать DTO из массива данных WP каталога
     * Очищает название от префикса вида "1 ", "2 " и т.д.
     */
    public static function fromWpArray(array $data, ?int $parentId = null): self
    {
        $name = $data['name'];
        // Удаляем цифровой префикс с пробелом в начале (например "1 Мебель" -> "Мебель")
        $name = preg_replace('/^\d+\s+/', '', $name);

        return new self(
            wpId: (int) $data['id'],
            name: $name,
            parentId: $parentId,
        );
    }
}
