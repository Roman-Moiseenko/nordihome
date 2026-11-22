<?php

namespace App\Modules\Shop\Domain\Schema;

readonly class ProductSchema implements SchemaElement
{
    /**
     * @param string[] $image
     * @param array<int, array{name: string, url: string, image?: string}> $category
     * @param array{name: string, logo?: string} $brand
     */
    public function __construct(
        private string $name,
        private string $description,
        private array $image = [],
        private string $sku = '',
        private ?SchemaElement $offer = null,
        private string $url = '',
        private string $mpn = '',
        private string $id = '',
        private array $category = [],
        private string $color = '',
        private ?float $width = null,
        private ?float $height = null,
        private ?float $depth = null,
        private array $brand = [],
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type'       => 'Product',
            'name'        => $this->name,
            'description' => $this->description,
            'image'       => $this->image,
            'url'         => $this->url,
            'sku'         => $this->sku,
        ];

        if ($this->mpn !== '') {
            $data['mpn'] = $this->mpn;
        }

        if ($this->id !== '') {
            $data['id'] = $this->id;
        }

        if (!empty($this->category)) {
            $data['category'] = $this->category;
        }

        if ($this->color !== '') {
            $data['color'] = $this->color;
        }

        if ($this->width !== null) {
            $data['width'] = $this->quantitativeValue('CMT', $this->width);
        }

        if ($this->height !== null) {
            $data['height'] = $this->quantitativeValue('CMT', $this->height);
        }

        if ($this->depth !== null) {
            $data['depth'] = $this->quantitativeValue('CMT', $this->depth);
        }

        if (!empty($this->brand)) {
            $data['brand'] = $this->brand;
        }

        if ($this->offer) {
            $data['offers'] = $this->offer->toArray();
        }

        return $data;
    }

    /**
     * @return array{@type: string, unitCode: string, value: string}
     */
    private function quantitativeValue(string $unitCode, float $value): array
    {
        return [
            '@type'    => 'QuantitativeValue',
            'unitCode' => $unitCode,
            'value'    => rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.'),
        ];
    }
}
