<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Setting\Application\Actions\Concerns\RunsArtisanCommand;

class RecacheUseCase
{
    use RunsArtisanCommand;

    /**
     * Пересоздание (прогрев) кеша модуля Shop.
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $this->runArtisan('shop:cache-warm');

        return 'Кеш пересоздан, задача прогрева поставлена в очередь';
    }
}
