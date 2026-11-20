<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Services\Avito;

/**
 * Логирование загрузки объявлений Авито в отдельный файл.
 *
 * Путь к файлу — storage/logs/avito_import.log. Файл перезаписывается при
 * старте новой загрузки и удаляется командой очистки объявлений.
 */
class AvitoProductImportLogger
{
    public const string LOG_FILE = 'avito_import.log';

    public function start(): void
    {
        file_put_contents($this->path(), '');
    }

    public function categoryNotFound(string $categoryPath): void
    {
        $this->line("Категория не найдена: {$categoryPath}");
    }

    public function parameterNotFound(string $sheet, string $categoryPath, string $parameter): void
    {
        $this->line("Параметр не найден: лист={$sheet}, категория={$categoryPath}, параметр={$parameter}");
    }

    public function valueError(string $sheet, string $categoryPath, string $parameter, string $value, string $reason): void
    {
        $this->line("Ошибка значения: лист={$sheet}, категория={$categoryPath}, параметр={$parameter}, значение=\"{$value}\", причина={$reason}");
    }

    public function line(string $message): void
    {
        file_put_contents(
            $this->path(),
            '[' . now()->toDateTimeString() . '] ' . $message . PHP_EOL,
            FILE_APPEND | LOCK_EX,
        );
    }

    public function getFilePath(): string
    {
        return $this->path();
    }

    public function clear(): void
    {
        if (is_file($this->path())) {
            unlink($this->path());
        }
    }

    private function path(): string
    {
        return storage_path('logs/' . self::LOG_FILE);
    }
}
