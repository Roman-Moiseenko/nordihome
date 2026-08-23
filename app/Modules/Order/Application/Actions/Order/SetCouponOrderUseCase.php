<?php

namespace App\Modules\Order\Application\Actions\Order;

use App\Modules\Discount\Entity\Coupon;
use App\Modules\Order\Application\DTOs\Order\DiscountOrderData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Carbon\Carbon;

class SetCouponOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface    $repository,
        private OrderLoggerServiceInterface $logger,
    )
    {
    }

    public function execute(int $orderId, string $couponCode, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();

        $orderEntity = $this->repository->getById($orderId);


        //MAINDO КУПОН


        /*

            if (empty($couponCode)) {
                if (!is_null($order->coupon_id)) {
                    $this->logger->log(orderId: $order->id, action: 'Удалена скидка по купону',
                        object: $order->coupon->code, old: $order->coupon->bonus);

                    $order->coupon_id = null;
                    $order->coupon_amount = 0;
                }
            } else {

                $coupon = Coupon::where('code', $couponCode)
            ->where('client_id', $order->client_id)
            ->where('started_at', '<', Carbon::now())
            ->where('finished_at', '>', Carbon::now())
            ->where('status', Coupon::NEW)
            ->first();

                if (is_null($coupon)) throw new \DomainException('Неверный код купона');
        //        if ($coupon->started_at->gt(now())) throw new \DomainException('Купон еще не действует');
//                if ($coupon->finished_at->lt(now())) throw new \DomainException('Купон уже не действует');
                $order->coupon_id = $coupon->id;
                $this->logger->log(orderId: $order->id, action: 'Скидка по купону',
                    object: 'Установлена',
                    value: $coupon->bonus);
            }

            $order->save();
            $this->recalculation($order);

         */

        $orderEntity->recalculateTotals();

        $this->repository->save($orderEntity);

        $this->logger->log(orderId: $orderEntity->id, action: empty($couponCode)  ? 'Купон сброшен' : 'Установлен купон',
            value: $couponCode);
    }
}
