<?php

namespace App\Modules\Order\Application\Actions;

use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;

readonly class GetOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,

    )
    {

    }
    public function execute(int $id): OrderEntity
    {


        return $this->repository->getById($id);
    }
}
