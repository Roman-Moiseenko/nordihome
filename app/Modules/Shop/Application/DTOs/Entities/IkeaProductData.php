<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Entities;

use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;

class IkeaProductData
{
    /** @param ImageInfoData[] $images */
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly string  $code,
        public readonly string  $model,
        public float   $price,
        public readonly string  $short,
        public readonly string  $description,
        public readonly bool    $fragile,
        public readonly bool    $sanctioned,
        public readonly bool    $availability,
        public readonly int     $packs,
        public readonly array   $composite,
        public readonly array   $quantity,
        public readonly array   $colors,
        public readonly array   $packages,
        public readonly array   $images,
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
