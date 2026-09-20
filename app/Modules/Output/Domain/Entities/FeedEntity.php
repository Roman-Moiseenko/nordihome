<?php

declare(strict_types=1);

namespace App\Modules\Output\Domain\Entities;

final class FeedEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?bool $active = null {
        get => $this->active;
        set => $this->active = $value;
    }

    /** @var int[] */
    public array $productsIn = [] {
        get => $this->productsIn;
        set => $this->productsIn = $value;
    }

    /** @var int[] */
    public array $productsOut = [] {
        get => $this->productsOut;
        set => $this->productsOut = $value;
    }

    /** @var int[] */
    public array $categoriesIn = [] {
        get => $this->categoriesIn;
        set => $this->categoriesIn = $value;
    }

    /** @var int[] */
    public array $categoriesOut = [] {
        get => $this->categoriesOut;
        set => $this->categoriesOut = $value;
    }

    /** @var int[] */
    public array $roomsIn = [] {
        get => $this->roomsIn;
        set => $this->roomsIn = $value;
    }

    /** @var int[] */
    public array $roomsOut = [] {
        get => $this->roomsOut;
        set => $this->roomsOut = $value;
    }

    /** @var int[] */
    public array $promotionsIn = [] {
        get => $this->promotionsIn;
        set => $this->promotionsIn = $value;
    }

    /** @var int[] */
    public array $promotionsOut = [] {
        get => $this->promotionsOut;
        set => $this->promotionsOut = $value;
    }

    /** @var int[] */
    public array $groupsIn = [] {
        get => $this->groupsIn;
        set => $this->groupsIn = $value;
    }

    /** @var int[] */
    public array $groupsOut = [] {
        get => $this->groupsOut;
        set => $this->groupsOut = $value;
    }

    /** @var int[] */
    public array $tagsIn = [] {
        get => $this->tagsIn;
        set => $this->tagsIn = $value;
    }

    /** @var int[] */
    public array $tagsOut = [] {
        get => $this->tagsOut;
        set => $this->tagsOut = $value;
    }

    public bool $setPreprice = false {
        get => $this->setPreprice;
        set => $this->setPreprice = $value;
    }

    public ?string $setTitle = null {
        get => $this->setTitle;
        set => $this->setTitle = $value;
    }

    public ?string $setDescription = null {
        get => $this->setDescription;
        set => $this->setDescription = $value;
    }

    public ?\DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?\DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
