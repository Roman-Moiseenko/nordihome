<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\DTOs\Search;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * TrackSearchClickData — клик по элементу поисковой выдачи из JS.
 *
 * Содержит строку поиска (query), по которой строилась выдача, и выбранный
 * результат (id, тип, позиция). Строка запроса фиксируется через
 * TrackSearchUseCase, выбор — через RegisterSearchClickUseCase.
 */
class TrackSearchClickData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $query,
        #[Required, Numeric]
        public readonly int $resultId,
        #[Required, StringType, Max(50)]
        public readonly string $resultType,
        #[Required, Numeric]
        public readonly int $position,
        #[Nullable, Numeric]
        public readonly ?int $pageViewId,
    ) {}
}
