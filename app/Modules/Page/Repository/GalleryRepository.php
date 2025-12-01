<?php

namespace App\Modules\Page\Repository;

use App\Modules\Base\Entity\Photo;
use App\Modules\Page\Entity\Gallery;
use Illuminate\Http\Request;

class GalleryRepository
{

    public function getIndex(Request $request): array
    {
        return Gallery::orderBy('name')
            ->get()
            ->map(fn(Gallery $gallery) => $this->GalleryToArray($gallery))->toArray();
    }

    public function GalleryToArray(Gallery $gallery): array
    {
        return array_merge($gallery->toArray(), [
            'count' => $gallery->photos()->count(),
        ]);
    }

    public function GalleryWithToArray(Gallery $gallery): array
    {
        return array_merge($gallery->toArray(), [
            'photos' => $gallery
                ->photos()
                ->get()
                ->map(fn(Photo $photo) => array_merge($photo->toArray(), [
                    'url' => $photo->getUploadUrl(),
                ]))
                ->toArray(),
        ]);
    }

    public function AllImages(Request $request)
    {
        /** @var Gallery[] $galleries */
        $galleries = Gallery::orderBy('name')->getModels();
        $images = [];
        foreach ($galleries as $gallery) {
            $prefix = count($galleries) == 1 ? '' : $gallery->name . '.';

            foreach ($gallery->photos as $photo) {

                $name = !empty($photo->slug)
                    ? $photo->slug
                    : (!empty($photo->title) ? $photo->title : $photo->id);
                $images[] = [
                    'title' => $prefix . $name,
                    'value' => $photo->getUploadUrl(),
                ];
            }

        }
        return $images;
    }
}
