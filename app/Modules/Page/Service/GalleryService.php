<?php

namespace App\Modules\Page\Service;

use App\Modules\Base\Entity\Photo;
use App\Modules\Page\Entity\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryService
{

    public function createGallery(Request $request): Gallery
    {
        $gallery = Gallery::register(
            $request->string('name')->trim()->value(),
            $request->string('slug')->trim()->value(),
        );
        $gallery->description = $request->string('description')->trim()->value();
        return $gallery;
    }

    public function deleteGallery(Gallery $gallery): void
    {
        if ($gallery->photos()->count() > 0) throw new \DomainException('Нельзя удалить не пустую галерею!');
        $gallery->delete();
    }

    public function setInfo(Gallery $gallery, Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        $gallery->name = $name;
        $gallery->slug = empty($slug) ? Str::slug($name) : $slug;

        $gallery->description = $request->string('description')->trim()->value();
        $gallery->save();
    }

    public function delPhoto(Photo $photo): void
    {
        $photo->delete();
    }

    public function setPhoto(Photo $photo, Request $request): void
    {
        $photo->slug = $request->string('slug')->trim()->value();
        $photo->alt = $request->string('alt')->trim()->value();
        $photo->title = $request->string('title')->trim()->value();
        $photo->description = $request->string('description')->trim()->value();
        $photo->save();
    }

    public function addPhoto(Gallery $gallery, Request $request): Photo
    {
        $file = $request->file('file');
        if (is_null($file))  throw new \DomainException('Нет файла');
        $photo = Photo::upload($file);
        $gallery->photos()->save($photo);
        $photo->refresh();
        return $photo;
    }
}
