<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Application\DTOs\Brand\BrandCreateData;
use App\Modules\Catalog\Domain\Entities\BrandEntity;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateBrandUseCase
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    public function execute(BrandCreateData $dto, UserPermission $userPermission): BrandEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $brand = new BrandEntity(name: trim($dto->name));

        return $this->brandRepository->save($brand);
    }
}
