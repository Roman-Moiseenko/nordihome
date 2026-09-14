<?php

namespace App\Modules\Shop\Application\DTOs\PageElements;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

final readonly class OgImage
{
    public function __construct(
        public string $url,
        public ?int   $width = null,
        public ?int   $height = null,
        public ?string $type = null,       // image/jpeg, image/png, ...
        public ?string $alt = null,
    ) {}

    public static function fromPath(string $url, ?string $mime = null, ?int $w = null, ?int $h = null, ?string $alt = null): self
    {
        return new self($url, $w, $h, $mime, $alt);
    }

    public static function fromData(ImageInfoData $data): self
    {
        return new self(
            url: $data->src,
            width: $data->width,
            height: $data->height,
            type: $data->format,
            alt: $data->alt,
        );
    }
}
