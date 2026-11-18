<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\ReCreatePhotoThumbsJob;
use App\Modules\Shared\Infrastructure\Mappers\ModelTypeMapper;
use App\Modules\Shared\Infrastructure\Models\Photo;

class ReCreateThumbsUseCase
{
    /**
     * Пересоздаёт миниатюры (сначала удаляет, затем создаёт) для всех фото,
     * кроме model_type из NO_THUMBS и всех parser.* моделей.
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $skip = array_merge(
            ModelTypeMapper::NO_THUMBS,
            array_values(array_filter(
                ModelTypeMapper::allTypes(),
                static fn(string $type): bool => str_starts_with($type, 'parser.'),
            )),
        );

        $modelTypes = array_values(array_diff(
            ModelTypeMapper::allTypes(),
            $skip,
        ));

        $ids = Photo::query()
            ->whereIn('model_type', $modelTypes)
            ->pluck('id');

        foreach ($ids as $id) {
            ReCreatePhotoThumbsJob::dispatch((int) $id)->onQueue(QueueName::PHOTO);
        }

        return sprintf(
            'Задач на пересоздание миниатюр поставлено в очередь "%s": %d.',
            QueueName::PHOTO,
            $ids->count(),
        );
    }
}
