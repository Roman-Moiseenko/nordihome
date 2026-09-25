<?php

namespace App\Modules\Base\Traits;

use App\Modules\Shared\Infrastructure\Models\Photo;

/**
 * @property Photo $icon
 */
trait IconField
{
    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type','icon')->withDefault();
    }

}
