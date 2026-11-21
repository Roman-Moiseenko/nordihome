<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Image;

final class TraderEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    public bool $default = false {
        get => $this->default;
        set => $this->default = $value;
    }

    public bool $active = false {
        get => $this->active;
        set => $this->active = $value;
    }

    public ?Image $logo = null {
        get => $this->logo;
        set => $this->logo = $value;
    }

    /** @var TraderOrganizationEntity[] */
    public array $organizations = [] {
        get => $this->organizations;
        set => $this->organizations = $value;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}
