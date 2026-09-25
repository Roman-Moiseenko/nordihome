<?php

namespace App\Modules\Base\Traits;

use App\Modules\Shared\Infrastructure\Models\Photo;

/**
 * @property Photo $image
 */
trait ImageField
{

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', 'image')->withDefault();
    }

    public function getImage(string $thumb = ''): ?string
    {
        if (is_null($this->image) || is_null($this->image->file)) return 'images/no-image.jpg';
        if (empty($thumb)) return $this->image->getUploadUrl();
        return $this->image->getThumbUrl($thumb);
    }

}
