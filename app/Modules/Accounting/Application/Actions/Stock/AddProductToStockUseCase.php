<?php

namespace App\Modules\Accounting\Application\Actions\Stock;

use App\Modules\Accounting\Application\DTOs\Stock\StockCreateData;
use App\Modules\Accounting\Domain\Entities\StockEntity;
use App\Modules\Accounting\Domain\Interfaces\StockRepositoryInterface;

readonly class AddProductToStockUseCase
{

    public function __construct(
        private StockRepositoryInterface $stockRepository,
    )
    {
    }

    public function execute(StockCreateData $dto): StockEntity
    {
        $entity = $this->stockRepository->findByProductId($dto->productId);
        if (is_null($entity)) {
            $entity = new StockEntity(
                productId: $dto->productId,
                quantity: $dto->quantity,
                reserve: $dto->reserve,
            );
        } else {
            $entity->quantity = $dto->quantity;
            $entity->reserve = $dto->reserve;
        }

        return $this->stockRepository->save($entity);
    }
}
