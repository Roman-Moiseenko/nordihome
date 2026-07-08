<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Base\Job\LoadingImageProduct;
use App\Modules\Catalog\Application\Services\LoadCategoryWpService;
use App\Modules\Catalog\Application\Services\LoadProductWpService;
use App\Modules\Catalog\Application\Services\LoadRoomWpService;
use App\Modules\Catalog\Infrastructure\Job\JobLoadProductWP;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use function public_path;


class LoadCommand extends Command
{

    const int CATALOG_ID = 2725;
    const int ROOM_ID = 2940;
    use ConfirmableTrait;
    private StorageService $storageService;

    protected $signature = 'wp:load
                {--type= : catalog / product / "пусто"}';
    protected $description = 'Загрузка данных {--type= : catalog / product / "пусто"}';

    public function handle(
        LoadCategoryWpService      $loadCategoryWpService,
        LoadRoomWpService          $loadRoomWpService,

    ): bool
    {
        if (!$this->confirmToProceed()) return false;

        $type = $this->option('type');

        $this->storageService = new StorageService();

        if ($type == 'catalog' || $type == null) {

            $this->warn('Начало загрузки каталога');
            $filename = public_path() . '/temp/catalog.txt';
            $f_c = fopen($filename, 'r');
            $data = fread($f_c, filesize($filename));
            fclose($f_c);
            $categories = json_decode($data, true);

            $cat_category = $categories[self::CATALOG_ID];
            $cat_room = $categories[self::ROOM_ID];

            $this->info('Загружаем категории');
            $countCat = $loadCategoryWpService->load($cat_category);
            $this->info('Загружено ' . $countCat);
            $this->info('Загружаем комнаты');
            $countRoom = $loadRoomWpService->load($cat_room);
            $this->info('Загружено ' . $countRoom);
        }

        if ($type == 'product' || $type == null) {
            $this->warn('Начало загрузки товаров');
            $filename = public_path() . '/temp/product.txt';
            $f_c = fopen($filename, 'r');
            $data = fread($f_c, filesize($filename));
            fclose($f_c);
            $products = json_decode($data, true);
            $count = 0;
            foreach ($products as $product) {
                JobLoadProductWP::dispatch($product);
                $count++;
                //if ($count > 0) break; //Временное для теста
            }
            $this->info('Загружено товаров ' . $count);
        }
        return true;
    }

}
