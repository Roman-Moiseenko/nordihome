<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\Entities;

use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use DateTimeImmutable;

class OrderEntity
{
    public ?int $id {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $number = null {
        get => $this->number;
        set => $this->number = $value;
    }

    public ?int $clientId = null {
        get => $this->clientId;
        set => $this->clientId = $value;
    }

    public int $traderId {
        get => $this->traderId;
        set => $this->traderId = $value;
    }

    public OrderSellType $type {
        get => $this->type;
        set => $this->type = $value;
    }

    public ?int $shopperId = null {
        get => $this->shopperId;
        set => $this->shopperId = $value;
    }

    public ?int $staffId = null {
        get => $this->staffId;
        set => $this->staffId = $value;
    }

    public ?int $discountId = null {
        get => $this->discountId;
        set => $this->discountId = $value;
    }

    public ?float $discountAmount = null {
        get => $this->discountAmount;
        set => $this->discountAmount = $value;
    }

    public ?float $couponAmount = null {
        get => $this->couponAmount;
        set => $this->couponAmount = $value;
    }

    public ?int $couponId = null {
        get => $this->couponId;
        set => $this->couponId = $value;
    }

    public float $manual = 0 {
        get => $this->manual;
        set => $this->manual = $value;
    }

    public ?string $comment = null {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public ?string $commentClient = null {
        get => $this->commentClient;
        set => $this->commentClient = $value;
    }

    public bool $isPickup = false {
        get => $this->isPickup;
        set => $this->isPickup = $value;
    }

    public ?Address $address = null {
        get => $this->address;
        set => $this->address = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    /** @var OrderHistoryStatusEntity|null */
    public mixed $status = null;

    /** @var OrderHistoryStatusEntity[] */
    public array $statuses = [];

    /** @var OrderAdditionEntity[] */
    public array $additions = [];

    /** @var OrderItemEntity[] */
    public array $items = [];

    public function __construct(
        int $traderId,
        OrderSellType $type,
        ?int $clientId = null,

    ) {
        $this->traderId = $traderId;
        $this->type = $type;
        $this->clientId = $clientId;
    }


    public function addItem($productId, $quantity, $basePrice, $sellPrice = null, $discountId = null, $discountType = null, $preorder = false): void
    {
        //При добавлении товара, добавляем, даже дубли, без изменения кол-ва
        /*
        foreach ($this->items as &$item) {
            //Если такой товар уже есть и тип заказа совпадает
            if ($item->productId == $productId && $item->preorder == $preorder) {
                $item->quantity += $quantity;
                return;
            }
        }
        */
        $orderItem = new OrderItemEntity($productId, $quantity, $basePrice, $sellPrice ?? $basePrice);
        $orderItem->discountId = $discountId;
        $orderItem->discountType = $discountType;
        $orderItem->preorder = $preorder;

        $this->items[] = $orderItem;

        $this->recalculateTotals();
    }

    public function addAddition($additionId, $amount = 0, $quantity = 1): void
    {
        $orderAddition = new OrderAdditionEntity($additionId);
        $orderAddition->amount = $amount;
        $orderAddition->quantity = $quantity;

        $this->additions[] = $orderAddition;
        $this->recalculateTotals();
    }

    public function addStatus(OrderStatus $status): void
    {
        $statusHistory = new OrderHistoryStatusEntity($status);
        $statusHistory->createdAt = new DateTimeImmutable();
        $this->statuses[] = $statusHistory;
        $this->status = $status->getValue();
    }

    public function recalculateTotals()
    {

    }
}
