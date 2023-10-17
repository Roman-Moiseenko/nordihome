<?php

namespace App\Trait;

use App\Entity\Picture;

/**
 * @property string $picture_json
 */
trait PictureTrait
{
    public Picture $picture;

    public function setPicture(Picture $picture): void
    {
        $this->picture = $picture;
    }


    public static function boot()
    {
        parent::boot();
        self::saving(function ($object) {
            $object->picture_json = json_encode($object->picture);
        });

        self::retrieved(function ($object) {
            $picture = json_decode($object->picture_json, true);
            $object->setPicture(Picture::create(
                $picture['pathImage'],
                $picture['tagIcon'],
                $picture['description'],
                $picture['imageDefault']
            ));

        });
    }

}
