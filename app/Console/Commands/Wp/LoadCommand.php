<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Base\Job\LoadingImageProduct;
use App\Modules\Catalog\Application\Services\LoadCategoryWpService;
use App\Modules\Catalog\Application\Services\LoadProductWpService;
use App\Modules\Catalog\Application\Services\LoadRoomWpService;
use App\Modules\Catalog\Entity\Brand;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use function public_path;


class LoadCommand extends Command
{

    const int CATALOG_ID = 2725;
    const int ROOM_ID = 2940;
    use ConfirmableTrait;

    private int $count_categories = 0;
    private int $count_products = 0;
    private PricingDocument $pricing;
    private string $storage;

    private StorageService $storageService;

    protected $signature = 'wp:load
    {--type= : catalog / product / "пусто"}';
    protected $description = 'Загрузка данных';
    private Common $common;

    public function handle(
        ListStaffByPositionUseCase $positionUseCase,
        LoadCategoryWpService $loadCategoryWpService,
        LoadRoomWpService $loadRoomWpService,
        LoadProductWpService $loadProductWpService,
    ): bool
    {
        if (! $this->confirmToProceed()) {
            return false;
        }
        $staffs = $positionUseCase->execute(StaffPosition::administrator());
        if (count($staffs) == 0) {return false;}
        $staff = $staffs[0];
        $this->storage = public_path() . '/temp/';
        $type = $this->option('type');

        $settings = app()->make(Settings::class);
        $this->common = $settings->common;

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
            //$this->loadCatalog();
        }

        if ($type == 'product' || $type == null) {
            $this->warn('Начало загрузки товаров');
            $filename = public_path() . '/temp/product.txt';
            $f_c = fopen($filename, 'r');
            $data = fread($f_c, filesize($filename));
            fclose($f_c);
            $products = json_decode($data, true);
            $loadProductWpService->load($products);
            /*
            $this->loadProduct();
            $pricingService = new PricingService();
            $this->pricing = PricingDocument::register($staff->id);
            $loadProductWpService->load();
            $this->loadProduct();

            $this->pricing->refresh();
            $pricingService->completed($this->pricing);
            */
        }
        return true;
    }
/*
    private function loadCatalog(): void
    {

        $this->warn('Начало загрузки каталога');
        $filename = public_path() . '/temp/catalog.txt';
        $f_c = fopen($filename, 'r');
        $data = fread($f_c, filesize($filename));
        fclose($f_c);
        $categories = json_decode($data, true);

        $cat_category = $categories[self::CATALOG_ID];
        $cat_room = $categories[self::ROOM_ID];


        foreach ($cat_category['children'] as $category) {
            $new_id = $this->create_category($category);

            if (!is_null($new_id)) $this->children($new_id, $category['children']);
        }

        $this->info('Каталоги загружены - ' . $this->count_categories);
    }
*/

    private function loadProduct(): void
    {
        $brand = Brand::whereIn('name', ['Икеа', 'Ikea', 'IKEA', 'ИКЕА'])->first();
        if ($brand == null) {
            $brand = Brand::register('Икеа');
        }
        $this->warn('Начало загрузки товаров');
        $filename = public_path() . '/temp/product.txt';
        $f_c = fopen($filename, 'r');
        $data = fread($f_c, filesize($filename));
        fclose($f_c);
        $products = json_decode($data, true);
        foreach ($products as $product) {
            $this->info($this->create_product($product, $brand->id));
        }
        $this->info('Товары загружены - ' . $this->count_products);
    }

    private function create_product($data, $brand_id): string
    {
        //MAINDO Переделать загрузку
        /**
         * Ищем по wp_id в Category (затем для Room тоже самое)
         * по полю wp_id из $data['categories'] если нашли, то проверяем дочерние, если дочерних нет,
         * то добавляем в массив категорий,
         * для Category 0 элемент - main_category_id, остальные дочерние
         * для Room - все списком в attach
         */

        $product_name = trim($data['name']);
        $product_code = trim($data['sku']);
        if (empty($product_code)) return '********************* ' . $product_name . ' **** нет артикула';

        if (Product::where('code', $product_code)->first() != null) return $product_code . '* уже создан *';
        if (Product::where('name', $product_name)->first() != null) $product_name = $product_name . ' ' . $product_code;
        if (empty($data['categories'])) return '********************* ' . $product_name . '* нет категории *';

        $this->count_products++;

        //Ищем категории
        $cat_ids = [];

        foreach ($data['categories'] as $data_cat) {
            $_cat = Category::where('name', $data_cat['name'])->first();
            if ($_cat != null) $cat_ids[] = $_cat->id;
        }
        if (empty($cat_ids))
            return '*********** ---error--- ' . $product_name . '(' . $product_code . ')' . json_encode($data['categories']);
        $product = Product::register($product_name, $product_code, $cat_ids[0]);
        //Вторичные категории
        for ($i = 1; $i < count($cat_ids); $i++) {
            $product->categories()->attach($cat_ids[$i]);
        }
        //Бренд
        $product->brand_id = $brand_id;
        //Описание
        $product->description = $data['description'];
        $product->short = $data['short'];
        $product->old_slug = $data['slug'];
        $product->save();
        //Цена
        $this->add_pricing($product->id, (float)$data['price']);

        //Изображения
        foreach ($data['images'] as $data_img) {
            $url = $data_img['url'];
            $alt = $data_img['alt'];
            LoadingImageProduct::dispatch($product, $url, $alt);
        }

        $product->pre_order = $this->common->pre_order;
        $product->only_offline = false;
        $product->local = $this->common->delivery_local;
        $product->delivery = $this->common->delivery_all;
        $product->save();
        $product->setPublished();
        $this->storageService->add_product($product);

        return $product->name . ' (' . $product->code . ')';
    }
/*
    private function children(int $parent_id, $children): void
    {
        if (empty($children)) return;
        foreach ($children as $child) {
            $new_id = $this->create_category($child, $parent_id);
            if (!is_null($new_id)) $this->children($new_id, $child['children']);
        }
    }
*/
    /*
    private function create_category($data, $parent_id = null):? int
    {
        $wp_id = $data['id'];
        $name = $data['name'];
        $file = $data['img'];


        if (!empty(Category::where('wp_id', $wp_id)->first())) return null;
        $this->count_categories++;

        $newCategory = Category::register($name, $parent_id);
        $newCategory->wp_id = $wp_id;
        //Загрузка изображения


        $newCategory->image()->save(Photo::uploadByUrl($file, 'image'));

        $newCategory->refresh();

        return $newCategory->id;
    }
*/
    private function add_pricing(int $product_id, float $retail): void
    {
        $this->pricing->pricingProducts()->save(
            PricingProduct::new($product_id, $retail / 2, $retail, 0, 0, $retail / 2, $retail)
        );
    }

}
