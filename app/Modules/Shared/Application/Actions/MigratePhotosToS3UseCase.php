<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Actions;

use App\Modules\Shared\Application\Interfaces\PhotoStorageInterface;

/**
 * Переносит файлы из одного хранилища (source) в другое (target).
 *
 * В контексте миграции на S3 source — локальное хранилище, target — S3.
 */
class MigratePhotosToS3UseCase
{
    public function __construct(
        private readonly PhotoStorageInterface $source,
        private readonly PhotoStorageInterface $target,
    )
    {
    }

    /**
     * @param list<string> $relativePaths Относительные пути файлов (например, "/uploads/..." или "/cache/...")
     * @param bool $removeSource Удалять исходный файл после успешной загрузки в target
     * @return int Количество успешно перенесённых файлов
     */
    public function execute(array $relativePaths, bool $removeSource = false): int
    {
        $migrated = 0;

        foreach ($relativePaths as $relativePath) {
            $localAbsolutePath = $this->source->localCopy($relativePath);

            if ($localAbsolutePath === null) {
                continue;
            }

            $this->target->putFromLocalFile($relativePath, $localAbsolutePath);

            if ($removeSource) {
                $this->source->delete($relativePath);
            }

            $migrated++;
        }

        return $migrated;
    }
}
