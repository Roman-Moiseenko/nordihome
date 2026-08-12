<?php

namespace App\Modules\Order\Application\Actions;

use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

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
