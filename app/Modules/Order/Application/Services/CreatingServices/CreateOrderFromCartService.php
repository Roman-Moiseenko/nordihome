<?php

namespace App\Modules\Order\Application\Services\CreatingServices;

use App\Modules\Accounting\Application\Actions\Trader\GetDefaultTraderIdUseCase;
use App\Modules\Auth\Application\Queries\GetInfoWebClientQuery;
use App\Modules\Cart\Application\Actions\GetCartUseCase;
use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Order\Application\Actions\AdditionGuide\GetDeliveryAdditionUseCase;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPolandAdditionUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Infrastructure\Events\OrderHasCreated;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
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
        private GetPolandAdditionUseCase $polandAdditionUseCase,
        private GetDeliveryAdditionUseCase $deliveryAdditionUseCase,
        private GetDefaultTraderIdUseCase $traderIdUseCase,
        private OrderLoggerServiceInterface $logger,
    )
    {

    }

    public function execute(ClientContext $clientContext, string|null $code, string|null $commentClient): OrderEntity
    {
        //FIXME Каждую задачу из // вынести в UseCase
        $this->transactionManager->execute(function () use ($clientContext, $code, $commentClient, &$orderEntity) {
            //Создаем пустой заказ
            $orderEntity = new OrderEntity(
                traderId: $this->traderIdUseCase->execute(),
                type: new OrderSellType(Order::ONLINE),
                clientId: $clientContext->id,
                priceType: new PriceType($clientContext->priceType));

            $isParser = false;
            $cartData = $this->cartUseCase->execute();
            foreach ($cartData->items as $item) {
                if ($item->check) {

                    if ($item->isParser) $isParser = true; //Хотя бы одна позиция на доставку
                    //Присоединяем к нему товары
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
                    //Удаляем товары из корзины
                    $this->removeCartItemUseCase->execute($item->id);

                }
            }

            $clientInfo = $this->getInfoClientQuery->execute($clientContext->id);
            //Добавляем базовые услуги //доставка из польши
            if ($isParser) {
                $addition = $this->polandAdditionUseCase->execute();
                $orderEntity->addAddition($addition->id);
            }

            //Добавляем доставку до региона или по региону
            if (!$clientInfo->isPickup) {
                $addition = $this->deliveryAdditionUseCase->execute($clientInfo->address->regionCode);
                $orderEntity->addAddition($addition->id);
            }

            //Применяем купон
            if (!is_null($code) && !is_null($coupon = $this->getCoupon($code, $clientContext->id))) {
                $orderEntity->couponId = $coupon->id;
            }

            //Данные из Клиента в Заказ
            $orderEntity->commentClient = $commentClient;
            $orderEntity->isPickup = $clientInfo->isPickup;
            if (!$clientInfo->isPickup) $orderEntity->address = $clientInfo->address;
            //Устанавливаем статусы
            $orderEntity->addStatus(OrderStatus::new());
            $orderEntity = $this->repository->save($orderEntity);

            //Пересчет скидок
            $this->orderCalculateService->execute($orderEntity->id);

            //FIXME Создание Lead тест
            $leadData = new LeadSourceData(
                id: $orderEntity->id,
                able: 'order.order',
                data: [],
                orderId: $orderEntity->id,
            );
            $this->dispatcher->dispatch(new LeadCollected($leadData));
            $this->dispatcher->dispatch(new OrderHasCreated($orderEntity->id));
            $this->logger->log($orderEntity->id, 'Заказ создан из корзины');
        });
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
