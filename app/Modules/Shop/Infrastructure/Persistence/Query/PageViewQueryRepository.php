<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Entities\PageData;
use App\Modules\Shop\Application\Helpers\ImageInfoDataHelper;
use Illuminate\Support\Facades\DB;

class PageViewQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'content.page';

    public function __construct(
        private readonly ImageInfoDataHelper $imageInfoHelper,
    )
    {
    }

    public function getPageBySlug(string $slug): ?PageData
    {
        $row = DB::table('pages')
            ->where('pages.slug', $slug)
            ->where('pages.published', true)
            ->leftJoin('photos', function ($join) {
                $join->on('pages.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::PHOTO_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'pages.id',
                'pages.name',
                'pages.slug',
                'pages.text',
                'pages.template',
                'pages.meta',
                'pages.old_render',
                'pages.published_at',
                'pages.updated_at',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.alt as photo_alt',
                'photos.title as photo_title',
                'photos.description as photo_description',
                'photos.format as photo_format',
                'photos.width as photo_width',
                'photos.height as photo_height',
                'photos.model_type as model_type',
            )
            ->first();

        if (!$row) return null;

        $meta = json_decode($row->meta ?? '{}', true);

        return new PageData(
            id: (int)$row->id,
            slug: $row->slug,
            name: $row->name,
            title: $meta['title'] ?? '',
            description: $meta['description'] ?? '',
            fragment: $this->fragment($row->text ?? ''),
            text: $row->text ?? '',
            template: $row->template ?? '',
            oldRender: (bool)$row->old_render,
            image: $this->imageInfoHelper->build($row),
            publishedAt: $this->toIso8601($row->published_at ?? null),
            updatedAt: $this->toIso8601($row->updated_at ?? null),
        );
    }

    private function fragment(string $text): string
    {
        $text = trim($text);
        $pos = mb_strpos($text, "\n");

        return $pos === false ? $text : mb_substr($text, 0, $pos);
    }

    private function toIso8601(?string $value): string
    {
        return $value ? \Carbon\Carbon::parse($value)->toIso8601String() : '';
    }
}
