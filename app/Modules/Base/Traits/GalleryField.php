<?php

namespace App\Modules\Base\Traits;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Photo[] $gallery - для бэкенда, изображения относящиеся к товару
 * @property Photo[] $photos - для фронтенда, изображения с учетом модификации
 */
trait GalleryField
{
    public function gallery(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
    }
/*
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
*/
  /*  public function getImage(?string $thumb = null): string
    {
        $image = $this->photos()->where('sort', 0)->first();

        if (is_null($image)) return '/images/no-image.jpg';
        return is_null($thumb) ? $image->getUploadUrl() : $image->getThumbUrl($thumb);
    }


    public function miniImage(): string
    {
        $image = $this->gallery()->first();
        if (is_null($image)) {
            if (!is_null($this->modification) && !is_null($this->photos)) return '/images/modification.jpg';
            return '/images/no-image.jpg';
        }
        return $image->getThumbUrl('mini');
    }
*/

}
