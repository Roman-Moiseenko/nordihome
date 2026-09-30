<?php

declare(strict_types=1);

namespace App\Modules\Shared\Presentation\Console\Commands;

use App\Modules\Shared\Application\DTOs\JobPhotoMigrateData;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\MigratePhotosToS3Job;
use App\Modules\Shared\Infrastructure\Storage\LocalPhotoStorage;
use Illuminate\Console\Command;

class MigratePhotosToS3Command extends Command
{
    protected $signature = 'photo:migrate-to-s3
                            {--remove-source : Удалять локальный файл после успешной загрузки в S3}
                            {--chunk=100 : Количество файлов в одной задаче очереди}
                            {--test : Тест, запуск одной очереди}';


    protected $description = 'Перенос изображений из локального хранилища в облачное S3 (задачи ставятся в очередь photo)';

    public function __construct(
        private readonly LocalPhotoStorage $localStorage,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $chunkSize = (int) $this->option('chunk');
        if ($chunkSize < 1) {
            $chunkSize = 100;
        }

        $removeSource = (bool) $this->option('remove-source');
        $test = (bool) $this->option('test');

        $files = array_merge(
            $this->localStorage->listFiles('/uploads'),
            $this->localStorage->listFiles('/cache'),
        );

        if ($files === []) {
            $this->info('Файлов для переноса не найдено.');
            return self::SUCCESS;
        }

        $chunks = array_chunk($files, $chunkSize);
        $progress = $this->output->createProgressBar(count($chunks));
        $progress->start();

        foreach ($chunks as $chunk) {
            MigratePhotosToS3Job::dispatch(
                new JobPhotoMigrateData(paths: $chunk, removeSource: $removeSource)
            )->onQueue(QueueName::PHOTO);

            $progress->advance();
            if ($test) {
                $this->info('Тестовый запуск');
                break;
            }
        }

        $progress->finish();
        $this->newLine();

        $this->info(sprintf(
            'В очередь "%s" поставлено задач: %d (файлов: %d).',
            QueueName::PHOTO,
            count($chunks),
            count($files),
        ));

        return self::SUCCESS;
    }
}
