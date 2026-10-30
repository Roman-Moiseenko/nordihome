<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Job;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создаёт все thumbs (mini, catalog, card, original) для одного фото.
 * В конструктор приходит только id записи из таблицы photos.
 */
class CreatePhotoThumbsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $photoId,
    )
    {
    }

    public function handle(PhotoService $photoService, Settings $settings): void
    {
        try {
            $photo = Photo::find($this->photoId);
            if ($photo === null) {
                return;
            }

            foreach ($settings->getImage()->thumbs as $thumb) {
                $photoService->createThumbs(
                    photoId: $photo->id,
                    modelType: $photo->model_type,
                    imageableId: (int)$photo->imageable_id,
                    fileName: $photo->file,
                    thumb: $thumb['name'],
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('CreatePhotoThumbsJob failed', [
                'photo_id' => $this->photoId,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
