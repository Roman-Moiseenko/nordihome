<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Photo[] $gallery - для бэкенда, изображения относящиеся к товару
 * @property Photo[] $photos - для фронтенда, изображения с учетом модификации
 */
trait GalleryField
{
    protected bool $is_thumb = true;

    public function gallery(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
    }

    public function photos(): MorphMany
    {
        if ($this instanceof Product) {
            $query = $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
            if ($query->count() > 0) return $query; //У товара есть изображения, в противном случае:
            //Если есть модификация и текущий продукт не базовый (Избежание зацикливания, когда у базового нет изображений)
            if (!is_null($this->modification) && $this->modification->base_product_id != $this->id) {
                return $this->modification->base_product->gallery(); //Изображения из базового
            }
        }
        return $this->gallery();
    }

    public function getImageBySort(int $sort, string $thumb = null): string
    {
        /** @var Photo $image */
        $image = $this->photos()->where('sort', $sort)->first();
        if (is_null($image)) return '/images/no-image.jpg';
        return is_null($thumb) ? $image->getUploadUrl() : $image->getThumbUrl($thumb);
    }

    public function getImage(string $thumb = null): string
    {
        return $this->getImageBySort(0, $thumb);
    }

    public function getImageNext(string $thumb = null): string
    {
        return $this->getImageBySort(1, $thumb);
    }

    public function getImageData(string $thumb = null): array
    {
        $image = $this->photos()->first();
        return $this->ImageToData($image, $thumb);

    }

    public function getImageNextData(string $thumb = null): array
    {
        $image = $this->photos()->skip(1)->first();
        if (is_null($image)) $image = $this->photos()->first();
        return $this->ImageToData($image, $thumb);
    }

    private function ImageToData(Photo $image, string $thumb = null): array
    {
        if (is_null($image)) return [
            'src' => '/images/no-image.jpg',
            'alt' => '',
            'title' => '',
            'description' => '',
        ];
        return [
            'src' => is_null($thumb) ? $image->getUploadUrl() : $image->getThumbUrl($thumb),
            'alt' => $image->alt,
            'title' => $image->alt,
            'description' => $image->description,
        ];
    }

    /**
     * Для списка товаров в админке
     */
    public function miniImage(): string
    {
        $image = $this->gallery()->first();
        if (is_null($image)) {
            if (!is_null($this->modification) && !is_null($this->photos)) return '/images/modification.jpg';
            return '/images/no-image.jpg';
        }
        return $image->getThumbUrl('mini');
    }

    public function addImage($file): Photo
    {
        if (empty($file)) throw new \DomainException('Нет файла');

        $sort = count($this->gallery);
        $photo = Photo::upload(file: $file, sort: $sort, thumb: $this->is_thumb);
        $this->gallery()->save($photo);
        $photo->refresh();
        return $photo;
    }

    public function addImageByUrl(string $url): ?Photo
    {
        if (empty($url)) return null;

        $sort = count($this->gallery);
        $photo = Photo::uploadByUrl(url: $url, sort: $sort, thumb: $this->is_thumb);
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

    public function copyImage(Photo $image): void
    {
        $sort = count($this->gallery);
        $photo = Photo::copyByPath(path: $image->getUploadFile(), sort: $sort, thumb: $this->is_thumb);
        $this->gallery()->save($photo);
    }

}
