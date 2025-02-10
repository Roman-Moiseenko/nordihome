<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Image extends AbstractSetting
{
    public string $watermark_file = '';
    public float $watermark_size = 0.2; //от размера изображения
    public string $watermark_position = 'bottom-right';
    public int $watermark_offset = 20;

    public bool $createThumbsOnSave = true;
    public bool $createThumbsOnRequest = true;

    public array $thumbs = [];

    //'__name__' => ['width' => int, 'height' => int, 'fit' => true, 'watermark' => true],

}
