<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity\Options;

class Image
{
    public array $watermark;
    public bool $createThumbsOnSave;
    public bool $createThumbsOnRequest;
    public array $thumbs;
    public array $path;
    public array $default;

    public static function createFromArray(array $image): self
    {
        $_image = new static();
        $_image->watermark = $image['watermark'];
        $_image->watermark['file'] = public_path() . $_image->watermark['file'];
        $_image->createThumbsOnSave = $image['createThumbsOnSave'];
        $_image->createThumbsOnRequest = $image['createThumbsOnRequest'];
        $_image->thumbs = $image['thumbs'];
        $_image->path = $image['path'];
        $_image->default = $image['default'];
        return $_image;
    }

    public function getPublicPath(string $string): string
    {
        return  public_path() . $this->path[$string];
    }


}
