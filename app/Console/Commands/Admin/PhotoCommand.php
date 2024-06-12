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

class PhotoCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'photo:patch';
    protected $description = 'Исправление сортировки в фотографиях товаров';

    public function handle(): bool
    {

        if (! $this->confirmToProceed()) {
            return false;
        }

        $this->info('Процесс исправления запущен');
        /** @var Product[] $products */
        $products = Product::where('published', true)->get();
        $this->info('Кол-во товаров - ' . $products->count());
        $_count = 0;
        foreach ($products as $product) {
            $change = false;


            foreach ($product->photos as $i => $photo) {
                if ($photo->sort != $i) {
                    $change = true;
                    $photo->sort = $i;
                    $photo->save();
                }
            }

            if ($change) {
                $_count++;
                $this->info('   ***** ' . $product->name . ' Изображений ' . $product->photos()->count());
            }

        }
        $this->info('Отсортированы изображения ' . $_count. ' товаров');

        return true;
    }
}
