<?php
declare(strict_types=1);

namespace App\Entity;

class Dimensions
{
    public float $width;
    public float $height;
    public float $depth;
    public float $weight;

    public static function create($width, $height, $depth, $weight): self
    {
        $dimension = new static();
        $dimension->width = $width;
        $dimension->height = $height;
        $dimension->depth = $depth;
        $dimension->weight = $weight;

        return $dimension;
    }
}
