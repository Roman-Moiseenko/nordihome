<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class Packages
{
    /** @var Package[] $packages */
    public array $packages = [];


    public function volume(): float
    {
        $volume = 0;
        foreach ($this->packages as $package) {
            $volume += $package->volume();
        }
        return $volume;
    }

    public function weight(): float
    {
        $weight = 0;
        foreach ($this->packages as $package) {
            $weight += $package->weight * $package->quantity;
        }
        return $weight;
    }

    public function create($height = 0, $width = 0, $length = 0, $weight = 0, int $quantity = 1, array $params = []): void
    {
        if (!empty($params)) {
            $height = (int)$params['height'];
            $width = (int)$params['width'];
            $length = (int)$params['length'];
            $weight = (int)$params['weight'];
            $quantity = (int)$params['quantity'];
        }
        $package = Package::create($height, $width, $length, $weight, $quantity);
        $this->packages[] = $package;
    }

    public function add(Package $package): void
    {
        $this->packages[] = $package;
    }

    public function clear(): void
    {
        $this->packages = [];
    }


    public function toArray(): array
    {
        return $this->packages;
    }

    public static function fromArray(string|null $json): self
    {
        $array = json_decode($json, true);
        if (is_null($json) || is_null($array)) return new Packages();
        $packages = new Packages();
        foreach ($array as $item) {
            $packages->create(
                $item['height'] ?? 0,
                $item['width'] ?? 0,
                $item['length'] ?? 0,
                $item['weight'] ?? 0,
                $item['quantity'] ?? 1
            );
        }

        return $packages;
    }
}
