<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Auth\Application\Queries\GetInfoWebClientQuery;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\DTOs\OrderItemData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use App\Modules\Shop\Application\Actions\Cart\GetCartUseCase;
use App\Modules\Shop\Application\Actions\Cart\RemoveCartItemUseCase;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Carbon\Carbon;
use Illuminate\Events\Dispatcher;

readonly class CreateOrderFromCartService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private GetCartUseCase              $cartUseCase,
        private RemoveCartItemUseCase       $removeCartItemUseCase,
        private GetInfoWebClientQuery       $getInfoClientQuery,
        private OrderCalculateService       $orderCalculateService,
        private Dispatcher                  $dispatcher,
        private OrderRepositoryInterface    $repository,
    )
    {

    }

    public function execute(ClientContext $clientContext, string|null $code, string|null $commentClient): OrderEntity
    {
        //FIXME Каждую задачу из // вынести в UseCase
       // $this->transactionManager->execute(function () use ($clientContext, $code, $commentClient, &$order) {
            //Создаем пустой заказ
            $trader_id = Trader::default()->organization->id;
            $orderEntity = new OrderEntity($trader_id, new OrderSellType(Order::ONLINE), $clientContext->id);

         //   $order = Order::register($clientContext->id, Order::ONLINE, $trader_id);

            $isParser = false;
            $cartData = $this->cartUseCase->execute();
            foreach ($cartData->items as $item) {
                if ($item->check) {

                    if ($item->isParser) $isParser = true; //Хотя бы одна позиция на доставку
                    //Присоединяем к нему товары
                    //DTO (orderId, productId, quantity, isPreorder (isParser), price)
                    $itemDto = new OrderItemData(
                        productId: $item->productId,
                        quantity: $item->quantity,
                        basePrice: $item->price,
                        sellPrice: $item->discountPrice ?? $item->price,
                        discountId: $item->discountId,
                        discountType: !is_null($item->discountId) ? Promotion::class : null,
                        preorder: $item->isParser,
                    );
                    $orderEntity->addItem($itemDto);

  /*
                    $orderItem = OrderItem::new($item->productId, $item->quantity, $item->isParser);
                    $orderItem->setCost($item->price, $item->price);
                    $orderItem->assemblage = false;
                    $orderItem->packing = false;
                    $order->items()->save($orderItem);
*/
                    //Удаляем товары из корзины
                    $this->removeCartItemUseCase->execute($item->id);

                }
            }

            $clientInfo = $this->getInfoClientQuery->execute($clientContext->id);
            //Добавляем базовые услуги //доставка из польши
            if ($isParser) {
                $addition = Addition::where('slug', 'poland')->first();
                //$orderAddition = OrderAddition::new($addition->id);
                $orderEntity->addAddition($addition->id);
                //$order->additions()->save($orderAddition);
            }

            //Добавляем доставку до региона или по региону
            if (!$clientInfo->isPickup) {
                //FIXME Сделать Query GetAdditionData какой нибудь
                if ($clientInfo->address->regionCode == 39) {
                    $addition = Addition::where('slug', 'koenig')->first();
                } else {
                    $addition = Addition::where('slug', 'russia')->first();
                }
                $orderEntity->addAddition($addition->id);
               // $orderAddition = OrderAddition::new($addition->id);
                //$order->additions()->save($orderAddition);
            }

            //Применяем купон
            if (!is_null($code) && !is_null($coupon = $this->getCoupon($code, $clientContext->id))) {
                $orderEntity->couponId = $coupon->id;
                //$order->coupon_id = $coupon->id;
                //$order->save();
            }

            //Данные из Клиента в Заказ
            $orderEntity->commentClient = $commentClient;
            $orderEntity->isPickup = $clientInfo->isPickup;
            if (!$clientInfo->isPickup) $orderEntity->address = $clientInfo->address;
            //Устанавливаем статусы
            $orderEntity->addStatus(OrderStatus::new());
            $order = $this->repository->save($orderEntity);
            //$order->save();

            //Пересчет скидок
            $this->orderCalculateService->execute($order->id);

            //FIXME Создание Lead тест
            $leadData = new LeadSourceData(
                id: $order->id,
                able: 'order.order',
                data: [],
                orderId: $order->id,
            );
            $this->dispatcher->dispatch(new LeadCollected($leadData));

      //  });
        return $orderEntity;
    }


    public function getCoupon(string $code, int $client_id): ?Coupon
    {

        $coupon = Coupon::where('code', $code)
            ->where('client_id', $client_id)
            ->where('started_at', '<', Carbon::now())
            ->where('finished_at', '>', Carbon::now())
            ->where('status', Coupon::NEW)
            ->first();
        if (!empty($coupon)) return $coupon;
        return null;
    }

    private function addProduct(Order $order, int $productId, float $quantity, float $price, bool $isParser)
    {

    }
}
