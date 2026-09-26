<?php

namespace App\Modules\Shared\Application\Interfaces;

interface PhotoStorageInterface
{
    /** Сохранить файл из бинарного содержимого */
    public function put(string $relativePath, string $contents): void;

    /** Сохранить файл, скопировав его из локального пути (например, tmp загрузки) */
    public function putFromLocalFile(string $relativePath, string $localAbsolutePath): void;

    /**
     * Вернуть абсолютный локальный путь к файлу.
     * Для S3 — скачивает во временный файл (его надо удалить после использования).
     * null — если файла нет.
     */
    public function localCopy(string $relativePath): ?string;

    public function exists(string $relativePath): bool;

    public function delete(string $relativePath): void;

    /** Удалить файлы по glob-маске внутри директории: deleteMatching('/cache/a/b/1', 'small_*.jpg') */
    public function deleteMatching(string $directoryRelativePath, string $globPattern): void;

    /** Публичный URL файла */
    public function url(string $relativePath): string;

    /**
     * Рекурсивный список относительных путей файлов в директории.
     * Нужен только для миграции.
     * @return string[]
     */
    public function listFiles(string $directoryRelativePath): array;
}
