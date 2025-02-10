<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Image extends AbstractSetting
{
    public string $watermark_file = '/image/watermark.png';
    public float $watermark_size = 0.2; //от размера изображения
    public string $watermark_position = 'bottom-right';
    public int $watermark_offset = 20;

    public bool $createThumbsOnSave = true;
    public bool $createThumbsOnRequest = true;

    public array $thumbs = [
        ['name' => 'mini', 'width' => 80, 'height' => 80,],
        ['name' => 'catalog', 'width' => 320, 'height' => 320,],
        ['name' => 'card', 'width' => 700, 'height' => 700,],
        ['name' => 'original', 'watermark' => false],
    ];

    //'__name__' => ['width' => int, 'height' => int, 'fit' => true, 'watermark' => true],

}
