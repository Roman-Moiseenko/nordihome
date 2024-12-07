<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property Photo $image
 */
trait ImageField
{

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', 'image')->withDefault();
    }

    public function saveImage($file, bool $clear_current = false): void
    {
        if ($clear_current && !(is_null($this->image) || is_null($this->image->file)))
            $this->image->delete();

        if (empty($file)) return;

        $this->image->newUploadFile($file, 'image');
    }

    public function getImage(string $thumb = ''): ?string
    {
        if (is_null($this->image) || is_null($this->image->file)) return null;
        if (empty($thumb)) return $this->image->getUploadUrl();
        return $this->image->getThumbUrl($thumb);
    }

 /*   protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getImage(),
        );
    }*/
}
