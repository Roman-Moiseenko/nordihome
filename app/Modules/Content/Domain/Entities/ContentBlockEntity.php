<?php

namespace App\Modules\Content\Domain\Entities;

use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Domain\ValueObjects\ContentSection;
use DateTimeImmutable;

final class ContentBlockEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }
    public ?string $caption = null {
        get => $this->caption;
        set => $this->caption = $value;
    }
    public ContainerType $containerType {
        get => $this->containerType;
    }

    public int $containerId {
        get => $this->containerId;
    }

    public ?int $widgetInstanceId = null {
        get => $this->widgetInstanceId;
        set => $this->widgetInstanceId = $value;
    }

    public ?int $sort = null {
        get => $this->sort;
        set => $this->sort = $value;
    }

    public ?ContentSection $section = null {
        get => $this->section;
        set => $this->section = $value;
    }

    public bool $active = true {
        get => $this->active;
        set => $this->active = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public ?WidgetInstanceEntity $widgetInstance = null {
        get => $this->widgetInstance;
        set => $this->widgetInstance = $value;
    }
    public function __construct(
        ContainerType $containerType,
        int $containerId,
    ) {
        $this->containerType = $containerType;
        $this->containerId = $containerId;
        $this->widgetInstanceId = null;
        $this->section = ContentSection::content();
    }

}
