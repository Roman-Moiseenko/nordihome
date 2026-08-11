<?php
declare(strict_types=1);

namespace App\Modules\Order\Infrastructure\Persistence;

use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderAdditionEntity;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use App\Modules\Order\Infrastructure\Models\OrderHistoryStatus;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use DateTimeImmutable;
use DomainException;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    /** @return OrderEntity[] */
    public function getAll(): array
    {
        return Order::with(['status', 'statuses', 'items', 'additions'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(Order $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): OrderEntity
    {
        $model = Order::with(['status', 'statuses', 'items', 'additions'])
            ->find($id);

        if (!$model) {
            throw new DomainException("Заказ с id={$id} не найден");
        }

        return $this->hydrate($model);
    }

    public function save(OrderEntity $order): OrderEntity
    {
        $model = $order->id
            ? Order::findOrFail($order->id)
            : new Order();

        $model->client_id = $order->clientId;
        $model->trader_id = $order->traderId;
        $model->type = (string) $order->type;
        $model->shopper_id = $order->shopperId;
        $model->staff_id = $order->staffId;
        $model->discount_id = $order->discountId;
        $model->discount_amount = $order->discountAmount;
        $model->coupon_amount = $order->couponAmount;
        $model->coupon_id = $order->couponId;
        $model->manual = $order->manual;
        $model->comment = $order->comment;
        $model->comment_client = $order->commentClient;
        $model->is_pickup = $order->isPickup;

        if ($order->address !== null) {
            $model->country = $order->address->country;
            $model->region_code = $order->address->regionCode;
            $model->region = $order->address->region;
            $model->city = $order->address->city;
            $model->street = $order->address->street;
            $model->postal_code = $order->address->postalCode;
        }

        $model->number = $order->number;

        $model->save();

        return $this->hydrate(
            $model->fresh()->load(['status', 'statuses', 'items', 'additions'])
        );
    }

    public function delete(int $id): void
    {
        $model = Order::find($id);
        if (!$model) {
            throw new DomainException("Заказ с id={$id} не найден");
        }
        $model->delete();
    }

    /** @return LengthAwarePaginator<OrderEntity> */
    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Order::with(['status', 'statuses', 'items', 'additions'])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Order $model) => $this->hydrate($model));
    }

    /** @return LengthAwarePaginator<OrderEntity> */
    public function getByClientId(int $clientId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Order::with(['status', 'statuses', 'items', 'additions'])
            ->where('client_id', $clientId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Order $model) => $this->hydrate($model));
    }

    // ====================== Hydration ======================

    private function hydrate(Order $model): OrderEntity
    {
        $entity = new OrderEntity(
            traderId: $model->trader_id,
            type: new OrderSellType($model->type),
            clientId: $model->client_id,
        );

        $entity->id = $model->id;
        $entity->number = $model->number;
        $entity->shopperId = $model->shopper_id;
        $entity->staffId = $model->staff_id;
        $entity->discountId = $model->discount_id;
        $entity->discountAmount = $model->discount_amount;
        $entity->couponAmount = $model->coupon_amount;
        $entity->couponId = $model->coupon_id;
        $entity->manual = (float) $model->manual;
        $entity->comment = $model->comment;
        $entity->commentClient = $model->comment_client;
        $entity->isPickup = (bool) $model->is_pickup;

        if (!empty($model->city) || !empty($model->street)) {
            $entity->address = new Address(
                country: $model->country ?? '',
                city: $model->city ?? '',
                street: $model->street ?? '',
                region: $model->region,
                postalCode: $model->postal_code,
                regionCode: $model->region_code,
            );
        }

        if ($model->created_at instanceof \DateTimeInterface) {
            $entity->createdAt = DateTimeImmutable::createFromInterface($model->created_at);
        }
        if ($model->updated_at instanceof \DateTimeInterface) {
            $entity->updatedAt = DateTimeImmutable::createFromInterface($model->updated_at);
        }

        if ($model->relationLoaded('status') && $model->status) {
            $entity->status = $this->hydrateStatus($model->status);
        }

        if ($model->relationLoaded('statuses')) {
            $entity->statuses = $model->statuses
                ->sortBy('created_at')
                ->map(fn(OrderHistoryStatus $s) => $this->hydrateStatus($s))
                ->values()
                ->all();
        }

        if ($model->relationLoaded('items')) {
            $entity->items = $model->items
                ->map(fn(OrderItem $item) => $this->hydrateItem($item))
                ->all();
        }

        if ($model->relationLoaded('additions')) {
            $entity->additions = $model->additions
                ->map(fn(OrderAddition $addition) => $this->hydrateAddition($addition))
                ->all();
        }

        return $entity;
    }

    private function hydrateStatus(OrderHistoryStatus $model): OrderHistoryStatusEntity
    {
        $entity = new OrderHistoryStatusEntity(
            orderStatus: new OrderStatus($model->value),
        );
        $entity->orderId = $model->order_id;
        $entity->id = $model->id;
        $entity->comment = $model->comment;
        $entity->numberDocument = $model->number_document;
        $entity->dateDocument = $model->date_document;

        if ($model->created_at instanceof \DateTimeInterface) {
            $entity->createdAt = DateTimeImmutable::createFromInterface($model->created_at);
        }

        return $entity;
    }

    private function hydrateItem(OrderItem $model): OrderItemEntity
    {
        $entity = new OrderItemEntity(
            productId: $model->product_id,
            quantity: (float) $model->quantity,
            baseCost: (float) $model->base_cost,
            sellCost: (float) $model->sell_cost,
        );

        $entity->orderId = $model->order_id;
        $entity->id = $model->id;
        $entity->preorder = (bool) $model->preorder;
        $entity->discountId = $model->discount_id;
        $entity->discountType = $model->discount_type;
        $entity->fixManual = (bool) $model->fix_manual;
        $entity->options = is_array($model->options) ? $model->options : [];
        $entity->comment = $model->comment;
        $entity->reserveId = $model->reserve_id;
        $entity->assemblage = (bool) $model->assemblage;
        $entity->packing = (bool) $model->packing;

        if ($model->created_at instanceof \DateTimeInterface) {
            $entity->createdAt = DateTimeImmutable::createFromInterface($model->created_at);
        }
        if ($model->updated_at instanceof \DateTimeInterface) {
            $entity->updatedAt = DateTimeImmutable::createFromInterface($model->updated_at);
        }

        return $entity;
    }

    private function hydrateAddition(OrderAddition $model): OrderAdditionEntity
    {
        $entity = new OrderAdditionEntity(
            additionId: $model->addition_id,
        );
        $entity->orderId = $model->order_id;
        $entity->id = $model->id;
        $entity->amount = (float) $model->amount;
        $entity->comment = $model->comment;
        $entity->quantity = $model->quantity;

        if ($model->created_at instanceof \DateTimeInterface) {
            $entity->createdAt = DateTimeImmutable::createFromInterface($model->created_at);
        }
        if ($model->updated_at instanceof \DateTimeInterface) {
            $entity->updatedAt = DateTimeImmutable::createFromInterface($model->updated_at);
        }

        return $entity;
    }
}
