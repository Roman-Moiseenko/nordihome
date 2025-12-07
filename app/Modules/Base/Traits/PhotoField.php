<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property Photo $photo
 */
trait PhotoField
{
    protected bool $is_thumb = false;

    public function photo(): MorphOne
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function savePhoto($file, bool $clear_current = false): void
    {
        if ($clear_current && !(is_null($this->photo) || is_null($this->photo->file)))
            $this->photo->delete();

        if (empty($file)) return;
        $this->photo->newUploadFile($file, 'photo', $this->is_thumb);
    }

    public function getPhoto(string $thumb = ''): ?string
    {
        if (is_null($this->photo) || is_null($this->photo->file)) return null;
        if (empty($thumb)) return $this->photo->getUploadUrl();
        return $this->photo->getThumbUrl($thumb);
    }

    public function addImageByUrl(string $url): ?Photo
    {
        if (empty($url)) return null;
        $photo = Photo::uploadByUrl(url: $url, thumb: $this->is_thumb);
        $this->photo()->save($photo);
        $photo->refresh();
        return $photo;
    }
}
