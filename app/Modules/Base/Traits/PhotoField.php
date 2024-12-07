<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;

/**
 * @property Photo $photo
 */
trait PhotoField
{
    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', 'photo')->withDefault();
    }

    public function savePhoto($file, bool $clear_current = false): void
    {
        if ($clear_current && !(is_null($this->photo) || is_null($this->photo->file)))
            $this->photo->delete();

        if (empty($file)) return;
        $this->photo->newUploadFile($file, 'photo');
    }

    public function getIcon(string $thumb = ''): ?string
    {
        if (is_null($this->photo) || is_null($this->photo->file)) return null;
        if (empty($thumb)) return $this->photo->getUploadUrl();
        return $this->photo->getThumbUrl($thumb);
    }
}
