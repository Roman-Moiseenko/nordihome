<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Setting\Application\Actions\Concerns\RunsArtisanCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearImageCacheUseCase
{
    use RunsArtisanCommand;
    /**
     * Очищает кэш изображений и запускает пересоздание миниатюр
     * через команду photo:cache-regenerate.
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        return $this->runArtisan('photo:cache-regenerate');

    }
}
