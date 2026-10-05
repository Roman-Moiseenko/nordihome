<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveBrandUseCase
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        if ($this->brandRepository->hasProducts($id)) {
            throw new \DomainException('Нельзя удалить бренд с товарами');
        }

        $this->brandRepository->delete($id);
    }
}
