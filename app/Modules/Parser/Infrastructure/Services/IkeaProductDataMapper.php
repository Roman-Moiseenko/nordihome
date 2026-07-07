<?php

namespace App\Modules\Parser\Infrastructure\Services;

use App\Modules\Parser\Domain\ValueObjects\Package;

class IkeaProductDataMapper
{
    /** @param array $subProducts */
    public function mapComposite(array $subProducts): array
    {
        return array_map(function ($subProduct) {
            return [
                'code' => $this->toCode($subProduct['itemNo']),
                'quantity' => $subProduct['quantity'],
            ];
        }, $subProducts) ?? [];
    }

    /** @param array $packages */
    public function mapPackages(array $packages): array
    {
        $result = [];
        foreach ($packages as $_package) {
            if (!empty($measurementGroups = $_package['measurementGroups'])) {
                $_quantity = $_package['quantity']['value'];
                foreach ($measurementGroups as $itemGroup) {
                    $measurements = $itemGroup['measurements'];
                    $result[] = new Package(
                        height: $this->toHeight($measurements),
                        width: $this->toWidth($measurements),
                        length: $this->toLength($measurements),
                        weight: $this->toWeight($measurements),
                        quantity: $_quantity,
                    );
                }
            }
        }
        return $result;
    }

    public function toCode(string $code): string
    {
        if (empty($code)) return '';
        $code = substr_replace($code, '.', 6, 0);
        return substr_replace($code, '.', 3, 0);
    }

    private function toHeight(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "height") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);
    }

    private function fromDiameter(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "diameter") return $_measure['value'];
        }
        return 0.0;
    }

    private function toLength(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "length") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);

    }

    private function toWidth(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "width") return $_measure['value'];
        }
        return $this->fromDiameter($_measures);
    }
    private function toWeight(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "weight") return $_measure['value'];
        }
        return 0.0;
    }
}
