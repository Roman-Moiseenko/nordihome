<?php

namespace App\Modules\Shared\Domain\ValueObjects;

final class Meta
{
    private string $title;
    private string $description;

    public function __construct(string $title = '', string $description = '')
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function withTitle(string $title): self
    {
        $new = clone $this;
        $new->title = $title;
        return $new;
    }

    public function withDescription(string $description): self
    {
        $new = clone $this;
        $new->description = $description;
        return $new;
    }

    public function equals(self $other): bool
    {
        return $this->title === $other->title
            && $this->description === $other->description;
    }

    public static function default(): self
    {
        return new self();
    }

}
