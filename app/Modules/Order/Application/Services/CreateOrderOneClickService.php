<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Auth\Application\Services\FindOrCreateClientService;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Catalog\Application\Actions\ProductPrice\GetProductSellPriceUseCase;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\DTOs\OrderItemData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Infrastructure\Events\OrderHasCreated;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use App\Modules\Shop\Application\DTOs\Checkout\OneClickOrderData;
use Illuminate\Events\Dispatcher;

readonly class CreateOrderOneClickService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,

        //private OrderCalculateService       $orderCalculateService,
        private Dispatcher                  $dispatcher,

        private FindOrCreateClientService   $findOrCreateClientService,
        private GetProductSellPriceUseCase  $sellPriceUseCase,
        private OrderRepositoryInterface    $repository,
    )
    {

    }

    public function execute(OneClickOrderData $dto): ?OrderEntity
    {
        $this->transactionManager->execute(function () use ($dto, &$orderEntity) {
            $client = $this->findOrCreateClientService->execute($dto); //Ищем или создаем клиента
            //Получаем данные о товаре
            $product = $this->sellPriceUseCase->execute($dto->productId, $client->priceType);
            //Создаем заказ с клиентом
            $trader_id = Trader::default()->organization->id;
            $orderEntity = new OrderEntity($trader_id, new OrderSellType(Order::ONLINE), $client->id, $client->priceType);

            $orderEntity->isPickup = $dto->isPickup;
            if (!$dto->isPickup) {
                $orderEntity->address = new Address(
                    street: $dto->address,
                    region: $dto->region,
                    regionCode: $dto->regionCode,
                );
                //По доставке или нет, добавляем Доставку по региону или РФ, и вносим данные
                //FIXME Сделать Query GetAdditionData какой нибудь
                if ($orderEntity->address->regionCode == 39) {
                    $addition = Addition::where('slug', 'koenig')->first();
                } else {
                    $addition = Addition::where('slug', 'russia')->first();
                }
                $orderEntity->addAddition($addition->id);
            }

            //Добавляем продукт
            $orderEntity->addItem(new OrderItemData(
                productId: $dto->productId,
                quantity: 1.0,
                basePrice: $product->basePrice,
                sellPrice: $product->sellPrice,
                discountId: $product->discountId,
                discountType: $product->discountType,
            ));
            //Устанавливаем статусы
            $orderEntity->addStatus(OrderStatus::new());

            $orderEntity = $this->repository->save($orderEntity);
            //Отправляем событие
            $leadData = new LeadSourceData(
                id: $orderEntity->id,
                able: 'order.order',
                data: [],
                orderId: $orderEntity->id,
            );
            $this->dispatcher->dispatch(new LeadCollected($leadData));
            $this->dispatcher->dispatch(new OrderHasCreated($orderEntity->id));
        });
        return $orderEntity;
    }
}
