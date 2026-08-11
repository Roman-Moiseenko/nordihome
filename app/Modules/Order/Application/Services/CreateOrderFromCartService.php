<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Auth\Application\Queries\GetInfoWebClientQuery;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Guide\Entity\Addition;
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
    )
    {

    }

    public function execute(ClientContext $clientContext, string|null $code, string|null $commentClient): Order
    {
        //FIXME Каждую задачу из // вынести в UseCase
        $this->transactionManager->execute(function () use ($clientContext, $code, $commentClient, &$order) {
            //Создаем пустой заказ
            $trader_id = Trader::default()->organization->id;
            $order = Order::register($clientContext->id, Order::ONLINE, $trader_id);

            $isParser = false;
            $cartData = $this->cartUseCase->execute();
            foreach ($cartData->items as $item) {
                if ($item->check) {
                    if ($item->isParser) $isParser = true; //Хотя бы одна позиция на доставку
                    //Присоединяем к нему товары
                    //DTO (orderId, productId, quantity, isPreorder (isParser), price)
                    $orderItem = OrderItem::new($item->productId, $item->quantity, $item->isParser);
                    $orderItem->setCost($item->price, $item->price);
                    $orderItem->assemblage = false;
                    $orderItem->packing = false;
                    $order->items()->save($orderItem);

                    //Удаляем товары из корзины
                    $this->removeCartItemUseCase->execute($item->id);

                }
            }

            $clientInfo = $this->getInfoClientQuery->execute($clientContext->id);
            //Добавляем базовые услуги //доставка из польши
            if ($isParser) {
                $addition = Addition::where('slug', 'poland')->first();
                $orderAddition = OrderAddition::new($addition->id);
                $order->additions()->save($orderAddition);
            }

            //Добавляем доставку до региона или по региону
            if (!$clientInfo->isPickup) {
                //FIXME Сделать Query GetAdditionData какой нибудь
                if ($clientInfo->address->regionCode == 39) {
                    $addition = Addition::where('slug', 'koenig')->first();
                } else {
                    $addition = Addition::where('slug', 'russia')->first();
                }
                $orderAddition = OrderAddition::new($addition->id);
                $order->additions()->save($orderAddition);
            }

            //Применяем купон
            if (!is_null($code) && !is_null($coupon = $this->getCoupon($code, $clientContext->id))) {
                $order->coupon_id = $coupon->id;
                $order->save();
            }

            //Данные из Клиента в Заказ
            $order->comment_client = $commentClient;
            $order->is_pickup = $clientInfo->isPickup;
            if (!$clientInfo->isPickup) {
                $order->country = $clientInfo->address->country;
                $order->city = $clientInfo->address->city;
                $order->street = $clientInfo->address->street;
                $order->region = $clientInfo->address->region;
                $order->region_code = $clientInfo->address->regionCode;
                $order->postal_code = $clientInfo->address->postalCode;
            }
            $order->save();

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

        });
        return $order;
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
