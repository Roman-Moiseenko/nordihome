<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Jobs\LoadingImageProduct;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use function public_path;


class LoadCommand extends Command
{

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

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return false;
        }

        $this->storage = public_path() . '/temp/';
        $type = $this->option('type');

        $settings = app()->make(Settings::class);
        $this->common = $settings->common;

        $this->storageService = new StorageService();

        if ($type == 'catalog' || $type == null) {
            $this->loadCatalog();
        }
        if ($type == 'product' || $type == null) {

            $staff = Admin::where('role', Admin::ROLE_ADMIN)->first();
            $pricingService = new PricingService();
            $this->pricing = PricingDocument::register($staff->id);

            $this->loadProduct();

            $this->pricing->refresh();
            $pricingService->completed($this->pricing);
        }
        return true;
    }

    private function loadCatalog()
    {
        $this->warn('Начало загрузки каталога');
        $filename = public_path() . '/temp/catalog.txt';
        $f_c = fopen($filename, 'r');
        $data = fread($f_c, filesize($filename));
        fclose($f_c);
        $categories = json_decode($data, true);
        foreach ($categories as $category) {
            $this->info($this->create_category($category));
            $this->children($category['children']);
        }
        $this->info('Каталоги загружены - ' . $this->count_categories);
    }

    private function loadProduct()
    {
        $brand = Brand::where('name', 'Икеа')->first();
        if ($brand == null) {
            $brand = Brand::register('NONAME');
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
        $product->not_local = !$this->common->delivery_local;
        $product->not_delivery = !$this->common->delivery_all;
        $product->save();
        $product->setPublished();
        $this->storageService->add_product($product);

        return $product->name . ' (' . $product->code . ')';
    }

    private function children($children, &$level = 0)
    {
        $level++;
        $pred = str_repeat('-', $level);
        if (empty($children)) return false;
        foreach ($children as $child) {
            $this->info(' ' . $pred . ' ' . $this->create_category($child));
            $this->children($child['children'], $level);
        }
    }

    private function create_category($category)
    {
        $name = $category['name'];
        $parent = $category['parent'];
        $file = $category['img'];

        if (!empty(Category::where('name', $name)->first())) return $name . ' * уже создана *';
        $this->count_categories++;
        if (empty($parent) || is_array($parent)) {
            $result = Category::register($name);
        } elseif(is_string($parent)) {
            $cat_parent = Category::where('name', $parent)->first();

            $result = Category::register($name, $cat_parent->id);
        } else {
            return ' еrror - ' . json_encode($parent);
        }
        //Загрузка изображения
        $result->image()->save(Photo::uploadByUrl($file, 'image'));

        $result->refresh();

        return $result->slug;
    }

    private function add_pricing(int $product_id, float $retail)
    {
        $this->pricing->pricingProducts()->save(
            PricingProduct::new($product_id, 0, $retail, 0, 0, 0, 0)
        );
    }

}
