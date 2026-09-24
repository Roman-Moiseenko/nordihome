<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions\Concerns;

use Illuminate\Support\Facades\Process;

/**
 * Запускает консольную команду Artisan через исполняемый файл PHP.
 *
 * Определение бинарника PHP (в порядке приоритета):
 *  1. env PHP_BIN — явное указание, например PHP_BIN=php8.4;
 *  2. PHP_BINARY — текущий исполняемый файл PHP (работает в CLI и обычно в FPM);
 *  3. php{PHP_MAJOR_VERSION}.{PHP_MINOR_VERSION} — на unix-хостингах,
 *     например php8.4 (совпадает с командой `php8.4 artisan`).
 *
 * Таким образом локально выполняется `php artisan ...`, на сервере —
 * `php8.4 artisan ...`, а при обновлении версии PHP команда подхватится
 * автоматически (либо достаточно поменять PHP_BIN в .env).
 */
trait RunsArtisanCommand
{
    protected function runArtisan(string $command): string
    {
        $result = Process::path(base_path())
            ->timeout(600)
            ->run([$this->phpBinary(), 'artisan', $command]);

        if (!$result->successful()) {
            throw new \DomainException(
                'Не удалось выполнить команду "' . $command . '": '
                . trim($result->errorOutput() ?: $result->output())
            );
        }

        return trim($result->output());
    }

    protected function phpBinary(): string
    {
        $binary = env('PHP_BIN');

        if (is_string($binary) && $binary !== '') {
            return $binary;
        }

        if (PHP_BINARY !== '') {
            return PHP_BINARY;
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            return 'php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        }

        return 'php';
    }
}
