<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Setting\Application\Actions\Concerns\RunsArtisanCommand;

class ClearAllCacheUseCase
{
    use RunsArtisanCommand;

    /**
     * Полная очистка кеша приложения (config, route, view, cache и др.).
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $this->runArtisan('optimize:clear');

        return 'Кеш полностью очищен';
    }
}
