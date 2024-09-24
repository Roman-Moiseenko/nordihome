<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use JetBrains\PhpStorm\Pure;

class Package
{
    public float $height; //Z
    public float $width; //X
    public float $length; //Y


    public int $quantity;
    public float $weight;


    public function __construct()
    {

        $this->height = 0.0;
        $this->width = 0.0;
        $this->length = 0.0;
        $this->weight = 0.0;
        $this->quantity = 1;
    }

    #[Pure]
    public static function create($height, $width, $length, $weight, int $quantity = 1): self
    {
        $package = new static();
        $package->width = $width;
        $package->height = $height;
        $package->length = $length;
        $package->weight = $weight;
        $package->quantity = $quantity;
        return $package;
    }

    public function volume(): float
    {
        $volume = (int)(($this->height * $this->width * $this->length) * 100) / 100;
        return  $volume / 1000000;
    }

    public function toArray(): array
    {
        return [
           'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
        ];
    }
}
