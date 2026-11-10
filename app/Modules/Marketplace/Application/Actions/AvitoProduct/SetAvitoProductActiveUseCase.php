<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetAvitoProductActiveUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
    ) {}

    public function execute(int $id, bool $active, UserPermission $permission): AvitoProductEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);
        $product->active = $active;

        return $this->avitoProductRepository->save($product);
    }
}
