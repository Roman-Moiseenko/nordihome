<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Brand;

use App\Modules\Catalog\Application\DTOs\Brand\BrandListData;
use App\Modules\Catalog\Domain\Interfaces\BrandRepositoryInterface;

readonly class ListBrandUseCase
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    )
    {
    }

    /**
     * @return array{id: int, name: string, parser: ?string}[]
     */
    public function execute(): array
    {
        $brands = $this->brandRepository->getAll();

        return array_map(fn($brand) => BrandListData::fromEntity($brand),  $brands);
    }
}
