<?php
declare(strict_types=1);

namespace App\Modules\Order\Domain\Entities;

use App\Modules\Order\Domain\ValueObjects\OrderSellType;
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

    public ?string $country = null {
        get => $this->country;
        set => $this->country = $value;
    }

    public ?int $regionCode = null {
        get => $this->regionCode;
        set => $this->regionCode = $value;
    }

    public ?string $region = null {
        get => $this->region;
        set => $this->region = $value;
    }

    public ?string $city = null {
        get => $this->city;
        set => $this->city = $value;
    }

    public ?string $street = null {
        get => $this->street;
        set => $this->street = $value;
    }

    public ?string $postalCode = null {
        get => $this->postalCode;
        set => $this->postalCode = $value;
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
}
