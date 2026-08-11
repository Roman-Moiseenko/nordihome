<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\Entities;

use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Order\Application\DTOs\OrderItemData;
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


    public function addItem(OrderItemData $data): void
    {
        //При добавлении товара, добавляем, даже дубли, без изменения кол-ва
        /*
        foreach ($this->items as &$item) {
            //Если такой товар уже есть и тип заказа совпадает
            if ($item->productId == $data->productId && $item->preorder == $data->preorder) {
                $item->quantity += $data->quantity;
                return;
            }
        }
        */
        if (is_null($data->productId) || is_null($data->basePrice))
            throw new \InvalidArgumentException('productId или basePrice не доджны быть null');
        $orderItem = new OrderItemEntity(
            $data->productId,
            $data->quantity ?? 1,
            $data->basePrice,
            $data->sellPrice ?? $data->basePrice,
        );
        $orderItem->discountId = $data->discountId;
        $orderItem->discountType = $data->discountType;
        $orderItem->preorder = $data->preorder;

        $this->items[] = $orderItem;

        $this->recalculateTotals();
    }

    /**
     * Удаляет элемент заказа по его id.
     *
     * @param int $id
     * @return void
     */
    public function removeItem(int $id): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->id === $id) {
                array_splice($this->items, $index, 1);
                $this->recalculateTotals();
                return;
            }
        }
    }

    /**
     * Обновляет элемент заказа по его id.
     * Поля productId и baseCost неизменяемы после создания элемента.
     */
    public function updateItem(int $id, OrderItemData $data): void
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                if ($data->quantity !== null) $item->quantity = $data->quantity;
                if ($data->sellPrice !== null) $item->sellCost = $data->sellPrice;
                if ($data->discountId !== null) $item->discountId = $data->discountId;
                if ($data->discountType !== null) $item->discountType = $data->discountType;
                if ($data->preorder !== null) $item->preorder = $data->preorder;
                if ($data->fixManual !== null) $item->fixManual = $data->fixManual;
                if ($data->options !== null) $item->options = $data->options;
                if ($data->comment !== null) $item->comment = $data->comment;
                if ($data->assemblage !== null) $item->assemblage = $data->assemblage;
                if ($data->packing !== null) $item->packing = $data->packing;

                $this->recalculateTotals();
                return;
            }
        }
    }

    public function addAddition($additionId, $amount = 0, $quantity = 1): void
    {
        $orderAddition = new OrderAdditionEntity($additionId);
        $orderAddition->amount = $amount;
        $orderAddition->quantity = $quantity;

        $this->additions[] = $orderAddition;
        $this->recalculateTotals();
    }

    /**
     * Удаляет дополнение заказа по его id.
     *
     * @param int $id
     * @return void
     */
    public function removeAddition(int $id): void
    {
        foreach ($this->additions as $index => $addition) {
            if ($addition->id === $id) {
                array_splice($this->additions, $index, 1);
                $this->recalculateTotals();
                return;
            }
        }
    }

    /**
     * Обновляет дополнение заказа по его id.
     *
     * @param int $id
     * @param int|null $additionId
     * @param float|null $amount
     * @param int|null $quantity
     * @param string|null $comment
     * @return void
     */
    public function updateAddition(
        int $id,
        ?int $additionId = null,
        ?float $amount = null,
        ?int $quantity = null,
        ?string $comment = null,
    ): void {
        foreach ($this->additions as $addition) {
            if ($addition->id === $id) {
                if ($additionId !== null) $addition->additionId = $additionId;
                if ($amount !== null) $addition->amount = $amount;
                if ($quantity !== null) $addition->quantity = $quantity;
                if ($comment !== null) $addition->comment = $comment;

                $this->recalculateTotals();
                return;
            }
        }
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
