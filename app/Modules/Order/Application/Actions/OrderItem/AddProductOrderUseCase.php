<?php

namespace App\Modules\Order\Application\Actions\OrderItem;

use App\Modules\Catalog\Application\Actions\ProductPrice\GetProductSellPriceUseCase;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPolandAdditionUseCase;
use App\Modules\Order\Application\DTOs\OrderAddProductData;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Parser\Application\Actions\Product\GetParserPriceByProductUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AddProductOrderUseCase
{

    public function __construct(
        private OrderRepositoryInterface       $repository,
        private OrderCalculateService          $orderCalculateService,
        private GetProductSellPriceUseCase     $getProductSellPriceUseCase,
        private GetParserPriceByProductUseCase $getParserProductPriceUseCase,
        private GetPolandAdditionUseCase       $polandAdditionUseCase,
    )
    {
    }

    public function execute(int $orderId, OrderAddProductData $dto, UserPermission $permission): void
    {
        if (!$permission->can('order.order.edit')) throw new AccessDeniedException();
        $orderEntity = $this->repository->getById($orderId);
        $productPrice = $this->getProductSellPriceUseCase->execute($dto->productId, $orderEntity->priceType);


        $itemDto = new OrderItemData(
            productId: $dto->productId,
            quantity: $dto->quantity,
            basePrice: $productPrice->basePrice,
            sellPrice: !($dto->preorder) ? $productPrice->sellPrice : $this->getParserProductPriceUseCase->execute($dto->productId),
            discountId: !($dto->preorder) ? $productPrice->discountId : null,
            discountType: !($dto->preorder) ? $productPrice->discountType : null,
            preorder: $dto->preorder,
        );

        $orderEntity->addItem($itemDto, $dto->increase);

        if ($dto->preorder) {
            $addition = $this->polandAdditionUseCase->execute();
            $orderEntity->addAddition($addition->id);
        }

        $orderEntity = $this->repository->save($orderEntity);
        $this->orderCalculateService->execute($orderEntity->id);
    }
}
