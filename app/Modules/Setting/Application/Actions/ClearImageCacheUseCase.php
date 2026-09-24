<?php

declare(strict_types=1);

namespace App\Modules\Setting\Application\Actions;

use Illuminate\Support\Facades\File;

class ClearImageCacheUseCase
{
    /**
     * Удаляет все кешированные изображения из public/caches.
     *
     * @return string сообщение для пользователя
     */
    public function execute(): string
    {
        $path = public_path('caches');

        if (!File::isDirectory($path)) {
            return 'Кешированные изображения уже отсутствуют';
        }

        $count = count(File::allFiles($path)) + count(File::directories($path));

        File::cleanDirectory($path);

        return $count > 0
            ? 'Удалено кешированных изображений: ' . $count
            : 'Кешированных изображений не найдено';
    }
}
