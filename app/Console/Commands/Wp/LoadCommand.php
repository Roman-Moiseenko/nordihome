<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Entity\Photo;
use App\Jobs\LoadingImageProduct;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;


use Illuminate\Console\ConfirmableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use function public_path;

class LoadCommand extends Command
{

    use ConfirmableTrait;

    private int $count_categories = 0;
    private int $count_products = 0;
    private PricingDocument $pricing;
    private string $storage;
    private Options $options;

    protected $signature = 'wp:load
    {--type= : catalog / product / "пусто"}';
    protected $description = 'Загрузка данных';

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return false;
        }

        $this->storage = public_path() . '/temp/';
        $type = $this->option('type');
        $this->options = new Options(); //Настройки Магазина для товара
/*
        if ($type == null) {
            $this->loadCatalog();
            $this->pricing = PricingDocument::register($staff->id);
            $this->loadProduct();
            $this->pricing->refresh();
            $pricingService->completed($this->pricing);
        }*/
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
        $brand = Brand::orderBy('id')->first();
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
        if (empty($data['sku'])) return $data['name'] . ' **** нет артикула';
        //if (empty($data['sku'])) $data['sku'] = Str::uuid();
        if (Product::where('code', $data['sku'])->first() != null) return $data['sku'] . '* уже создан *';
        if (Product::where('name', $data['name'])->first() != null) $data['name'] = $data['name'] . ' ' . $data['sku'];
            //return $data['name'] . '* уже создан * - дубль имени';
        if (empty($data['categories'])) return $data['name'] . '* нет категории *';

        $this->count_products++;

        //Ищем категории
        $cat_ids = [];

        foreach ($data['categories'] as $data_cat) {
            $_cat = Category::where('name', $data_cat['name'])->first();
            if ($_cat != null) $cat_ids[] = $_cat->id;
        }
        if (empty($data['sku']) || empty($cat_ids))
            return '---error--- ' . $data['name'] . '(' . $data['sku'] . ')' . json_encode($data['categories']);
        $product = Product::register($data['name'], (string)$data['sku'], $cat_ids[0]);
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
        //$product->setPrice((float)$data['price']);
        //Изображения

        foreach ($data['images'] as $data_img) {
            $url = $data_img['url'];
            $alt = $data_img['alt'];
            LoadingImageProduct::dispatch($product, $url, $alt);
            //$upload_file_name = $this->copy_file($data_img);
            //$upload = new UploadedFile($this->storage . $upload_file_name, $upload_file_name, null, null, true);
  /*          try {
                $sort = count($product->photos);
                $product->photo()->save(Photo::uploadByUrl($url, '', $sort, $alt));
                $product->refresh();
            } catch (\Throwable $e) {
                $this->error('Файл не загрузился ' . $url);
            }
*/

        }
        $product->published = true;
        $product->pre_order = $this->options->shop->pre_order;
        $product->only_offline = $this->options->shop->only_offline;
        $product->not_local = !$this->options->shop->delivery_local;
        $product->not_delivery = !$this->options->shop->delivery_all;
        $product->save();
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
       // $upload_file_name = $this->copy_file($file);
       // $upload = new UploadedFile($this->storage . $upload_file_name, $upload_file_name, null, null, true);
        $result->image()->save(Photo::uploadByUrl($file, 'image'));
        //$result->image->newUploadFile($upload, 'image');
        $result->refresh();

        return $result->slug;
    }

    private function add_pricing(int $product_id, float $retail)
    {
        $this->pricing->pricingProducts()->save(
            PricingProduct::new($product_id, 0, $retail, 0, 0, 0)
        );
    }

/*
    private function copy_file(string $url): string
    {
        $filename = basename($url);

        if (copy($url, $this->storage . $filename)) {
            return $filename;
        }
        throw new \DomainException('Файл по ссылке ' . $url . ' не загрузился');
    }
*/
}
