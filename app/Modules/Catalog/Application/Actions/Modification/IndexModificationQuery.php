<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationIndexData;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class IndexModificationQuery
{
    public function __construct(
        private ModificationRepositoryInterface $modificationRepository,
    ) {
    }

    /**
     * Список модификаций с пагинацией: название, количество товаров,
     * изображение primary-товара и названия атрибутов-осей.
     *
     * @return LengthAwarePaginator<ModificationIndexData>
     */
    public function execute(UserPermission $permission, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        if (!$permission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->modificationRepository->findAll($perPage, $page);
    }
}
