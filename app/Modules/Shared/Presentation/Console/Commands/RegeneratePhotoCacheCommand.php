<?php

declare(strict_types=1);

namespace App\Modules\Shared\Presentation\Console\Commands;

use App\Modules\Shared\Application\Interfaces\PhotoStorageInterface;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\CreatePhotoThumbsJob;
use App\Modules\Shared\Infrastructure\Models\Photo;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Console\Command;

class RegeneratePhotoCacheCommand extends Command
{
    protected $signature = 'photo:cache-regenerate
                            {--model-type=* : Список model_type (можно указать несколько раз)}';

    protected $description = 'Пересоздаёт кэш миниатюр: удаляет /cache и ставит в очередь photo задачи для всех фото указанных model_type';

    private const DEFAULT_MODEL_TYPES = [
        'catalog.product',
        'catalog.category',
        'catalog.room',
        'catalog.brand',
    ];

    public function __construct(
        private readonly PhotoStorageInterface $storage,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $modelTypes = $this->option('model-type');
        if (!is_array($modelTypes) || $modelTypes === []) {
            $modelTypes = self::DEFAULT_MODEL_TYPES;
        }
        $modelTypes = array_values(array_unique($modelTypes));

        $this->storage->deleteDirectory(PhotoService::URL_THUMB);
        $this->info('Папка кэша миниатюр удалена.');

        $ids = Photo::query()
            ->whereIn('model_type', $modelTypes)
            ->pluck('id');

        foreach ($ids as $id) {
            CreatePhotoThumbsJob::dispatch((int)$id)->onQueue(QueueName::PHOTO);
        }

        $this->info(sprintf('Поставлено задач в очередь "%s": %d.', QueueName::PHOTO, $ids->count()));

        return self::SUCCESS;
    }
}
