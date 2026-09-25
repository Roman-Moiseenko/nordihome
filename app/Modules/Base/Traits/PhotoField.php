<?php

namespace App\Modules\Base\Traits;

use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property Photo $photo
 */
trait PhotoField
{
    public function photo(): MorphOne
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

}
