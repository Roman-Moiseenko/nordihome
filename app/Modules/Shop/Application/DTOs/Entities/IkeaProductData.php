<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

readonly class IkeaProductData
{
    /** @param ImageInfoData[] $images */
    public function __construct(
        public int     $id,
        public string  $name,
        public string  $slug,
        public string  $code,
        public string  $model,
        public float   $price,
        public string  $short,
        public string  $description,
        public bool    $fragile,
        public bool    $sanctioned,
        public bool    $availability,
        public int     $packs,
        public array   $composite,
        public array   $quantity,
        public array   $colors,
        public array   $packages,
        public array   $images,
    )
    {
    }

    public static function fromArray(array $item): self
    {
        return new self(
            id: $item['id'],
            name: $item['name'],
            slug: $item['slug'],
            code: $item['code'],
            model: $item['model'],
            price: $item['price'],
            short: $item['short'],
            description: $item['description'],
            fragile: $item['fragile'],
            sanctioned: $item['sanctioned'],
            availability: $item['availability'],
            packs: $item['packs'],
            composite: $item['composite'],
            quantity: $item['quantity'],
            colors: $item['colors'],
            packages: $item['packages'],
            images: array_map(
                fn(array $img) => ImageInfoData::fromArray($img),
                $item['images'],
            ),
        );
    }
}
