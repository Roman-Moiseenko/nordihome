<?php

namespace App\Modules\Lead\Domain\Entities;

use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use DateTimeImmutable;

final class LeadEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $staffId = null {
        get => $this->staffId;
        set => $this->staffId = $value;
    }

    public ?int $clientId = null {
        get => $this->clientId;
        set => $this->clientId = $value;
    }

    public ?int $orderId = null {
        get => $this->orderId;
        set => $this->orderId = $value;
    }

    public ?int $leadableId = null {
        get => $this->leadableId;
        set => $this->leadableId = $value;
    }

    public ?string $leadableType = null {
        get => $this->leadableType;
        set => $this->leadableType = $value;
    }

    public ?string $name = null {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?string $comment = null {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public int $canceled = 0 {
        get => $this->canceled;
        set => $this->canceled = $value;
    }

    public bool $completed = false {
        get => $this->completed;
        set => $this->completed = $value;
    }

    public bool $assembly = false {
        get => $this->assembly;
        set => $this->assembly = $value;
    }

    public bool $delivery = false {
        get => $this->delivery;
        set => $this->delivery = $value;
    }

    public ?DateTimeImmutable $finishedAt = null {
        get => $this->finishedAt;
        set => $this->finishedAt = $value;
    }

    /** @var LeadDataField[] */
    public array $data = [] {
        get => $this->data;
        set => $this->data = $value;
    }

    /** @var LeadItemEntity[] */
    public array $items = [] {
        get => $this->items;
        set => $this->items = $value;
    }

    /** @var LeadStatusEntity[] */
    public array $statuses = [] {
        get => $this->statuses;
        set => $this->statuses = $value;
    }

    public ?LeadStatusEntity $status = null {
        get => $this->status;
        set => $this->status = $value;
    }

    public DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    public DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
    }

    public function __construct(
        int $leadableId,
        string $leadableType,
        array $data
    ) {
        $this->leadableId = $leadableId;
        $this->leadableType = $leadableType;
        $this->data = $data;
    }

    public function addStatus(LeadStatusValue $value): void
    {
        $statusEntity = new LeadStatusEntity(value: $value);
        $this->addStatusEntity($statusEntity);
    }

    public function addStatusEntity(LeadStatusEntity $statusEntity): void
    {
        $statuses = $this->statuses;
        $statuses[] = $statusEntity;
        $this->statuses = $statuses;
        $this->status = $statusEntity;
    }

    public function addItem(LeadItemEntity $item): void
    {
        $items = $this->items;
        $items[] = $item;
        $this->items = $items;
    }

    public function addDataField(LeadDataField $field): void
    {
        $data = $this->data;
        $data[] = $field;
        $this->data = $data;
    }
}
