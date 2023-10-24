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
            $array['width'],
            $array['height'],
            $array['depth'],
            $array['weight'],
            $array['measure']
        );
    }
}
