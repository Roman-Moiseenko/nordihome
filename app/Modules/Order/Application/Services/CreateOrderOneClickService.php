<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Auth\Application\Actions\Client\CreateClientUseCase;
use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\DTOs\Client\ClientCreateData;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Auth\Application\Queries\GetInfoWebClientQuery;
use App\Modules\Auth\Application\Services\FindOrCreateClientService;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Catalog\Application\Actions\ProductPrice\GetProductSellPriceUseCase;
use App\Modules\Catalog\Application\DTOs\ProductPrice\ProductSellPriceData;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderAdditionEntity;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use App\Modules\Shop\Application\Actions\Cart\GetCartUseCase;
use App\Modules\Shop\Application\Actions\Cart\RemoveCartItemUseCase;
use App\Modules\Shop\Application\DTOs\Checkout\OneClickOrderData;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Illuminate\Events\Dispatcher;

readonly class CreateOrderOneClickService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,

        private OrderCalculateService       $orderCalculateService,
        private Dispatcher                  $dispatcher,

        private FindOrCreateClientService $findOrCreateClientService,

        private GetProductSellPriceUseCase $sellPriceUseCase,
        private OrderRepositoryInterface $repository,

    )
    {

    }

    public function execute(OneClickOrderData $dto): OrderEntity
    {
        $this->transactionManager->execute(function () use ($dto, &$order) {
            //FIXME Каждую задачу из // вынести в UseCase

            //Ищем или создаем клиента
            $client = $this->findOrCreateClientService->execute($dto);

            //Получаем данные о товаре
            $product = $this->sellPriceUseCase->execute($dto->productId, $client->priceType);
            //Создаем заказ с клиентом

            $orderItem = new OrderItemEntity($dto->productId, 1.0, $product->basePrice, $product->sellPrice);


            $orderItem->discountId = $product->discountId;
            $orderItem->discountType = $product->discountType;
            $trader_id = Trader::default()->organization->id;
            //Добавляем продукт

            $orderEntity = new OrderEntity($trader_id, new OrderSellType(Order::ONLINE), $client->id);
            $orderEntity->isPickup = $dto->isPickup;
            if (!$dto->isPickup) {
                $orderEntity->address = new Address(
                    country: null,
                    city: null,
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

                $additionEntity = new OrderAdditionEntity($addition->id);
                $orderEntity->additions = [$additionEntity];
            }
            $orderEntity->items = [$orderItem];

            //Устанавливаем статусы
            $status = new OrderHistoryStatusEntity(OrderStatus::new());
            $orderEntity->statuses = [$status];

            $order = $this->repository->save($orderEntity);

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
}
