<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Catalog\Entity\Product;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class PhotoCheckCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'photo:check';
    protected $description = 'Проверка пустых изображений товаров';

    public function handle(): bool
    {
        $this->info('Процесс исправления запущен');

        $photos = Photo::where('imageable_type', Product::class)->get();
        $this->info('Найдено ' . $photos->count() . ' изображений');
        //$photos = Photo::where('imageable_id', 4010)->get();
        /** @var Photo $photo */
        foreach ($photos as $photo) {
            if (is_null($photo->imageable)) {
                $path = public_path() . '/uploads/product/' . $photo->imageable_id . '/' . $photo->file;
                $this->info('id товара ');
                if (is_file($path)) unlink($path);
                $this->info('Файл удален');
                $photo->delete();
                $this->info('Сущность удалена');

            }
        }
        $this->info('Пересохранение запущено');

        return true;
    }
}
