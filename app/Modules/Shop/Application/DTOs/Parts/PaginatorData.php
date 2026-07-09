<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class PaginatorData
{
    public function __construct(
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly bool $hasPages,
        public readonly bool $onFirstPage,
        public readonly bool $hasMorePages,
        /** @var array */
        public readonly array $elements,
        /** @var array<int, string> map page=>url */
        public readonly array $urls,
        public readonly ?string $previousPageUrl,
        public readonly ?string $nextPageUrl,
    )
    {
    }
}
