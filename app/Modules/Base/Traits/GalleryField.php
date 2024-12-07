<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;

/**
 * @property Photo[] $gallery
 */
trait GalleryField
{
    public function gallery()
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
    }

    public function getImageBySort(int $sort, string $thumb = 'card'): string
    {
        /** @var Photo[] $images */
        $images = $this->gallery()->getModels();
        $count = count($images);
        if ($count == 0) return '';
        $pos = $sort - 1;
        if ( ($count - 1) < $pos ) $pos = $count - 1;
        return $images[$pos]->getThumbUrl($thumb);
    }

    public function addImage($file): void
    {
        if (empty($file)) throw new \DomainException('Нет файла');

        $sort = count($this->gallery);
        $this->gallery()->save(Photo::upload($file, '', $sort));
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

    public function upImage(int $photo_id)
    {
        //TODO Сделать
    }

    public function downImage(int $photo_id)
    {
        //TODO Сделать
    }

    protected function reSort(): void
    {
        foreach ($this->gallery as $i => $photo) {
            $photo->update(['sort' => $i]);
        }
    }

}
