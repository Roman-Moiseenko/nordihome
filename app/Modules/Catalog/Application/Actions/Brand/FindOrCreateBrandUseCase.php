<?php

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Application\Interfaces\BrandRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\BrandEntity;


readonly class FindOrCreateBrandUseCase
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    public function execute(string $name): BrandEntity
    {
        if (is_null($brand = $this->brandRepository->getByName($name))) {
            $brand = new BrandEntity($name);
            $brand = $this->brandRepository->save($brand);
        }
        return $brand;
    }
}
