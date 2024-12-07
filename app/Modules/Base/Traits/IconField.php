<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property Photo $icon
 */
trait IconField
{
    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type','icon')->withDefault();
    }

    public function saveIcon($file, bool $clear_current = false): void
    {
        if ($clear_current && !(is_null($this->icon) || is_null($this->icon->file)))
            $this->icon->delete();

        if (empty($file)) return;
        $this->icon->newUploadFile($file, 'icon');
    }

    public function getIcon(string $thumb = ''): ?string
    {
        if (is_null($this->icon) || is_null($this->icon->file)) return null;
        if (empty($thumb)) return $this->icon->getUploadUrl();
        return $this->icon->getThumbUrl($thumb);
    }
/*
    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getIcon(),
        );
    }*/
}
