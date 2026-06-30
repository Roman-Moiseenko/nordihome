<?php

namespace App\Modules\Shared\Domain\ValueObjects;

class Image
{
    public function __construct(
        private ?int $id,
        private readonly string  $url,

    ) {}

    public function getUrl(): string { return $this->url; }
    public function getId(): ?string { return $this->id; }
}
