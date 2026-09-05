<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Order\Application\DTOs\Order\FilterOrderIndexData;
use App\Modules\Order\Application\DTOs\Order\OrderIndexData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexOrderUseCase
{
    public function __construct(
        private  OrderRepositoryInterface $orderRepository,
        private ClientRepositoryInterface         $clientRepository,
        private StaffRepositoryInterface          $staffRepository,

    ){}
    public function execute(FilterOrderIndexData &$filter, UserPermission $permission): LengthAwarePaginator
    {
        if (!$permission->can('order.order.view')) throw new AccessDeniedException();

        $paginator = $this->orderRepository->getFilteredPaginated($filter);
        return $paginator->through(function(OrderEntity $order) {

            $clientEntity = $this->clientRepository->findById($order->clientId);
            $staffEntity = $this->staffRepository->findById($order->staffId);

            return new OrderIndexData(
                id: $order->id,
                statusPay: 0,
                statusOut: 0,
                createdAt: $order->createdAt->format('Y-m-d H:i:s'),
                number: $order->number,
                clientName: is_null($clientEntity) ? '-' : $clientEntity->fullName->getValue(),
                clientPhone: is_null($clientEntity) ? '' : $clientEntity->phone->getValue(),
                amount: $order->getTotalAmount(),
                status: $order->status->value->getValue(),
                statusName: $order->status->value->getName(),
                comment: $order->comment,
                staff: is_null($staffEntity) ? 'Не назначен' : $staffEntity->fullName->getValue(),
                refund: 0,
            );


        });
    }
}
