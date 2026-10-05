<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Application\DTOs\Brand\BrandUpdateData;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateBrandUseCase
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    public function execute(int $id, BrandUpdateData $dto, UserPermission $userPermission): BrandEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $brand = $this->brandRepository->getById($id);

        $brand->name = trim($dto->name);
        $brand->url = $dto->url ?? '';
        $brand->description = $dto->description ?? '';
        $brand->currencyId = $dto->currencyId;
        $brand->sameAs = $dto->sameAs ?? [];

        return $this->brandRepository->save($brand);
    }
}
