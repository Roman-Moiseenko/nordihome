<?php

namespace App\Modules\Cabinet\Application\Actions;

use App\Modules\Cabinet\Application\DTOs\OrderClientData;
use App\Modules\Cabinet\Application\DTOs\OrderInfoData;
use App\Modules\Cabinet\Application\DTOs\OrderInfoItemData;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\GetProductItemDataUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Shared\Application\Actions\GetPhotoThumbUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoThumbData;
use Illuminate\Support\Carbon;

readonly class GetOrderClientData
{
    public function __construct(
        private OrderRepositoryInterface  $repository,
        private GetProductItemDataUseCase $getProductItemData,
        private GetPhotoThumbUseCase      $getPhotoThumbUseCase,
        private GetAdditionDataUseCase    $getAdditionDataUseCase,
    )
    {
    }

    public function execute(int $orderId): OrderClientData
    {
        $orderEntity = $this->repository->getById($orderId);

        $items = [];
        $amount = 0;
        $baseAmount = 0;
        $amountAddition = 0;
        $delivery = 0;


        /** @var OrderItemEntity $item */
        foreach ($orderEntity->items as $item) {
            $productItemData = $this->getProductItemData->execute($item->productId);
            $dto = new PhotoThumbData(
                imageableId: $productItemData->id,
                modelType: 'catalog.product',
                type: 'gallery',
                thumb: 'mini',
            );


            $items[] = new OrderInfoItemData(
                productId: $item->productId,
                productName: $productItemData->name,
                productCode: $productItemData->code,
                productImage: $this->getPhotoThumbUseCase->execute($dto),
                //MAINDO !!!
                productPublished: true,
                productParser: false,
                productSlug: $productItemData->code,

                quantity: $item->quantity,
                baseCost: $item->baseCost,
                sellCost: $item->sellCost,
                preorder: $item->preorder,
                discountId: $item->discountId

            );
            $amount += $item->sellCost * $item->quantity;
            $baseAmount += $item->baseCost * $item->quantity;
        }
        $additions = [];
        foreach ($orderEntity->additions as $addition) {
            $additionGuide = $this->getAdditionDataUseCase->execute($addition->additionId, $orderEntity);
            if ($additionGuide->type == Addition::DELIVERY) {
                $delivery += $addition->amount;
                //if (!is_null($additionGuide->calculate)) $delivery += $additionGuide->calculate;
            }
            $additions[] = [];
            $amountAddition += $addition->amount;
            //if (!is_null($additionGuide->calculate)) $amountAddition += $additionGuide->calculate;
        }

        $info = new OrderInfoData(
            date: Carbon::instance($orderEntity->createdAt)->translatedFormat('d F Y'),
            number: $orderEntity->number,
            totalAmount: $amount + $amountAddition,
            baseAmount: $baseAmount + $amountAddition,
            status: $orderEntity->status->value->getValue(),
            statusName: $orderEntity->status->value->getName(),
            delivery: $delivery,
            address: $orderEntity->isPickup ? 'Самовывоз' : $orderEntity->address->getFullAddress(),
        );
        return new OrderClientData(
            id: $orderEntity->id,
            info: $info,
            items: $items,
            additions: $additions,
        );

    }
}
