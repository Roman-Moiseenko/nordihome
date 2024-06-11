<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use JetBrains\PhpStorm\Pure;

class Dimensions
{

    public float $width; //X
    public float $depth; //Y
    public float $height; //Z

    public int $type;
    public float $weight;
    public string $measure;

    const MEASURE_G = 'г';
    const MEASURE_KG = 'кг';

    const TYPE_DEPTH = 1;
    const TYPE_LENGTH = 2;
    const TYPE_DIAMETER = 3;

    //TODO Реализовать выбор и показ
    // Высота, Ширина, Глубина
    // Высота, Ширина, Длина
    // Высота, Диаметр

    const CAPTION_TYPES = [
        self::TYPE_DEPTH => ['высота', 'ширина', 'глубина'],
        self::TYPE_LENGTH => ['высота', 'ширина', 'длина'],
        self::TYPE_DIAMETER => ['высота', 'диаметр', null],
    ];

    public function __construct()
    {
        $this->measure = self::MEASURE_G;
        $this->width = 0.0;
        $this->height = 0.0;
        $this->depth = 0.0;
        $this->weight = 0.0;
    }

    public static function create($width, $height, $depth, $weight, $measure = self::MEASURE_G, $type = self::TYPE_DEPTH): self
    {
        $dimension = new static();
        $dimension->width = $width;
        $dimension->height = $height;
        $dimension->depth = $depth;
        $dimension->weight = $weight;
        $dimension->measure = $measure;
        $dimension->type = $type;

        return $dimension;
    }

    #[Pure] public static function fromArray(?array $params)
    {
        $dimension = new static();
        if (!empty($params)) {
            $dimension->width = $params['width'];
            $dimension->height = $params['height'];
            $dimension->depth = $params['depth'];
            $dimension->weight = $params['weight'];
            $dimension->measure = $params['measure'];
            $dimension->type = $params['type'];
        }
        return $dimension;
    }

    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'measure' => $this->measure ?? self::MEASURE_G,
            'type' => $this->type ?? self::TYPE_DEPTH,
        ];
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
        $volume = (int)(($this->height * $this->width * $this->depth) * 100) / 100;
        return  $volume / 1000000;
    }
}
