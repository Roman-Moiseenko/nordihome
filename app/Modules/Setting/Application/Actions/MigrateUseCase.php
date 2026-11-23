<?php

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Setting\Application\Actions\Concerns\RunsArtisanCommand;

class MigrateUseCase
{
    use RunsArtisanCommand;

    /**
     * Полная очистка кеша приложения (config, route, view, cache и др.).
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $this->runArtisan('migrate');

        return 'Миграция запущена';
    }
}
