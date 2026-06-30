<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Base\Job\ConvertPhotoProduct;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class PhotoToWebpCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'photo:webp';
    protected $description = 'Перекодирование всех изображений товара в WEBP';

    public function handle(): bool
    {
        $this->info('Процесс исправления запущен');

        $photos = Photo::where('imageable_type', Product::class)->get();
        $this->info('Найдено ' . $photos->count() . ' изображений');
        //$photos = Photo::where('imageable_id', 4010)->get();
        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $ext = pathinfo($photo->file, PATHINFO_EXTENSION);
            if ($ext != 'webp') {
                ConvertPhotoProduct::dispatch($photo);
                //$photo->convertToWebp();
            }
        }
        $this->info('Пересохранение запущено');

        return true;
    }
}
