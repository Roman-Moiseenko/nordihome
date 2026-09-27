<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\DTOs;

class JobPhotoMigrateData
{
    /**
     * @param list<string> $paths Относительные пути файлов для переноса из локального хранилища в S3
     * @param bool $removeSource Удалять локальный файл после успешной загрузки в S3
     */
    public function __construct(
        public readonly array $paths,
        public readonly bool $removeSource = false,
    )
    {
    }
}
