<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions\Concerns;

use Illuminate\Support\Facades\Process;

/**
 * Запускает консольную команду Artisan через текущий бинарник PHP.
 *
 * Используется PHP_BINARY, поэтому команда автоматически подхватывает
 * актуальный исполняемый файл PHP:
 *  - локально: php artisan ...
 *  - на сервере: php8.4 artisan ...
 *
 * При обновлении версии PHP команда изменится автоматически.
 */
trait RunsArtisanCommand
{
    protected function runArtisan(string $command): string
    {
        $result = Process::path(base_path())
            ->timeout(600)
            ->run([PHP_BINARY, 'artisan', $command]);

        if (!$result->successful()) {
            throw new \DomainException(
                'Не удалось выполнить команду "' . $command . '": '
                . trim($result->errorOutput() ?: $result->output())
            );
        }

        return trim($result->output());
    }
}
