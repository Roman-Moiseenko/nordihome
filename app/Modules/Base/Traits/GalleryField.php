<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Photo[] $gallery
 */
trait GalleryField
{
    public function gallery(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
    }

    public function getImageBySort(int $sort, string $thumb = null): string
    {
        /** @var Photo $image */
        $image = $this->gallery()->where('sort', $sort)->first();
        if (is_null($image)) return '/images/no-image.jpg';
        return  is_null($thumb) ? $image->getUploadUrl() : $image->getThumbUrl($thumb);

        /*
        $images = $this->gallery()->getModels();
        $count = count($images);
        if ($count == 0) return '';
        $pos = $sort - 1;
        if ( ($count - 1) < $pos ) $pos = $count - 1;
        return $images[$pos]->getThumbUrl($thumb);
        */
    }

    public function getImage(string $thumb = null): string
    {
        return $this->getImageBySort(0, $thumb);
    }

    public function getImageNext(string $thumb = null): string
    {
        return $this->getImageBySort(1, $thumb);
    }

    public function addImage($file): Photo
    {
        if (empty($file)) throw new \DomainException('Нет файла');

        $sort = count($this->gallery);
        $photo = Photo::upload($file, '', $sort);
        $this->gallery()->save($photo);
        $photo->refresh();
        return $photo;
    }

    public function addImageByUrl(string $url):? Photo
    {
        if (empty($url)) return null;

        $sort = count($this->gallery);
        $photo = Photo::uploadByUrl($url, '', $sort);
        $this->gallery()->save($photo);
        $photo->refresh();
        return $photo;
    }

    public function delImage(int $photo_id): void
    {
        $photo = Photo::find($photo_id);
        $photo->delete();
        $this->reSort();
    }

    public function setAlt(int $photo_id, string $alt = '', string $title = '', string $description = ''): void
    {
        foreach ($this->gallery as $photo) {
            if ($photo->id === $photo_id) {
                $photo->update([
                    'alt' => $alt,
                    'title' => $title,
                    'description' => $description,
                ]);
            }
        }
    }

    public function upImage(int $photo_id): void
    {
        $photos = [];
        foreach ($this->gallery as $photo) {
            $photos[] = $photo;
        }

        for ($i = 1; $i < count($photos); $i++) {
            if ($photos[$i]->id == $photo_id) {
                $prev = $photos[$i - 1]->sort;
                $next = $photos[$i]->sort;
                $photos[$i]->update(['sort' => $prev]);
                $photos[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function downImage(int $photo_id): void
    {
        $photos = [];
        foreach ($this->gallery as $photo) {
            $photos[] = $photo;
        }
        for ($i = 0; $i < count($photos) - 1; $i++) {
            if ($photos[$i]->id == $photo_id) {
                $prev = $photos[$i + 1]->sort;
                $next = $photos[$i]->sort;
                $photos[$i]->update(['sort' => $prev]);
                $photos[$i + 1]->update(['sort' => $next]);
            }
        }
    }

    public function reSort(): void
    {
        foreach ($this->gallery as $i => $photo) {
            $photo->update(['sort' => $i]);
        }
    }

}
