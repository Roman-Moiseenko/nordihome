<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Accounting\Application\Actions\Trader\GetDefaultTraderIdUseCase;
use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private ViewClientUseCase        $viewClientUseCase,
        private GetDefaultTraderIdUseCase $traderIdUseCase,

    ) {
    }
    public function execute(int|null $clientId, int $staffId, UserPermission $permission): OrderEntity
    {
        if (!$permission->can('order.order.create')) throw new AccessDeniedException();

        $orderEntity = new OrderEntity(
            traderId: $this->traderIdUseCase->execute(),
            type: new OrderSellType(OrderSellType::MANUAL),
            clientId: $clientId,
        );

        //Если клиент есть, заполняем данные
        if (!is_null($clientId)) {
            $client = $this->viewClientUseCase->execute($clientId, $permission);
            $orderEntity->priceType = $client->priceType;
            $orderEntity->address = $client->address;
            $orderEntity->isPickup = $client->isPickup;
        }
        $orderEntity->staffId = $staffId; //Ставим себя менеджером
        $orderEntity->addStatus(OrderStatus::new());
        return $this->repository->save($orderEntity);
    }
}
