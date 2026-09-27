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

}
