<?php
declare(strict_types=1);

namespace App\Entity;

class Dimensions
{
    public float $width;
    public float $height;
    public float $depth;
    public float $weight;
    public string $measure;

    const MEASURE_G = 'г';
    const MEASURE_KG = 'кг';

    public function __construct()
    {
        $this->measure = self::MEASURE_G;
        $this->width = 0.0;
        $this->height = 0.0;
        $this->depth = 0.0;
        $this->weight = 0.0;
    }

    public static function create($width, $height, $depth, $weight, $measure = self::MEASURE_G): self
    {
        $dimension = new static();
        $dimension->width = $width;
        $dimension->height = $height;
        $dimension->depth = $depth;
        $dimension->weight = $weight;
        $dimension->measure = $measure;

        return $dimension;
    }

    public function weight(): float
    {
        if ($this->measure === self::MEASURE_G) return $this->weight / 1000;
        return $this->weight;
    }

    public function toSave(): string
    {
        return json_encode([
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'measure' => $this->measure,
        ]);
    }

    public static function load(string $json): self
    {
        $array = json_decode($json, true);
        return self::create(
            $array['width'] ?? 0,
            $array['height'] ?? 0,
            $array['depth'] ?? 0,
            $array['weight'] ?? 0,
            $array['measure'] ?? self::MEASURE_G
        );
    }

    public function volume(): float
    {
        return ($this->height * $this->width * $this->depth) / 1000000;
    }
}
