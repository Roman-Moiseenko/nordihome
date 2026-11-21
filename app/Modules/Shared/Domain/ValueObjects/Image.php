<?php

namespace App\Modules\Shared\Domain\ValueObjects;

class Image
{
    public function __construct(
        private ?int $id,
        private readonly string  $url,
        private readonly int $width = 0,
        private readonly int $height = 0,
    ) {}

    public function getUrl(): string { return $this->url; }
    public function getId(): ?string { return $this->id; }
    public function getWidth(): int { return $this->width; }
    public function getHeight(): int { return $this->height; }
}
