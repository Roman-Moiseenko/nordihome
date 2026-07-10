<?php

namespace App\Modules\Shop\Application\DTOs\Parts;

class PaginatorData
{
    /**
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param int $lastPage
     * @param bool $hasPages
     * @param bool $onFirstPage
     * @param bool $hasMorePages
     * @param array $elements Формат как у LengthAwarePaginator:
     *                       каждый элемент — string (троеточие) или array [page => url]
     * @param array<int, string> $url map page=>url
     * @param string|null $previousPageUrl
     * @param string|null $nextPageUrl
     * @param int|null $firstPage
     * @param int|null $lastPage
     */
    public function __construct(
        public readonly int     $total,
        public readonly int     $perPage,
        public readonly int     $currentPage,
        public readonly int     $lastPage,
        public readonly bool    $hasPages,
        public readonly bool    $onFirstPage,
        public readonly bool    $hasMorePages,
        /** @var array */
        public readonly array   $elements,
        /** @var array<int, string> map page=>url */
        public readonly array   $url,
        public readonly ?string $previousPageUrl,
        public readonly ?string $nextPageUrl,
    )
    {
    }
}
