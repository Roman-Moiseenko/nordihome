<?php

namespace App\Modules\Shared\Domain\ValueObjects;

class Image
{
    public function __construct(
//        private ?int $id,
        private readonly string  $url,
        private readonly ?string $alt = null,
        // пока поля, которые реально нужны фронту
        // width/height добавите позже, если понадобятся
    ) {}

    public function getUrl(): string { return $this->url; }
    public function getAlt(): ?string { return $this->alt; }
}
