<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationViewData;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;

/**
 * Получение всех товаров модификации по ID одного из её товаров.
 *
 * Если товар не входит ни в одну модификацию — возвращает null.
 */
final readonly class GetModificationByProductQuery
{
    public function __construct(
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    public function execute(int $productId): ?ModificationViewData
    {
        return $this->modificationRepository->findViewDataByProductId($productId);
    }
}
