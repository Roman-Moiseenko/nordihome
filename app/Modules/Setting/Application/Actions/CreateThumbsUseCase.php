<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\CreatePhotoThumbsJob;
use App\Modules\Shared\Infrastructure\Mappers\ModelTypeMapper;
use App\Modules\Shared\Infrastructure\Models\Photo;

class CreateThumbsUseCase
{
    /**
     * Создаёт недостающие миниатюры для всех фото, кроме model_type из NO_THUMBS.
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $modelTypes = array_values(array_diff(
            ModelTypeMapper::allTypes(),
            ModelTypeMapper::NO_THUMBS,
        ));

        $ids = Photo::query()
            ->whereIn('model_type', $modelTypes)
            ->pluck('id');

        foreach ($ids as $id) {
            CreatePhotoThumbsJob::dispatch((int) $id)->onQueue(QueueName::PHOTO);
        }

        return sprintf(
            'Задач на создание миниатюр поставлено в очередь "%s": %d.',
            QueueName::PHOTO,
            $ids->count(),
        );
    }
}
