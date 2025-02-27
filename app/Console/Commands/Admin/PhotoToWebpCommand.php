<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Job\ConvertPhotoProduct;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\OrderReserve;

use App\Modules\Product\Entity\Product;
use App\Modules\Service\Entity\Report;
use App\Modules\Shop\Cart\Storage\DBStorage;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Entity\ParserStorage;
use App\Modules\User\Entity\Wish;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\confirm;

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
