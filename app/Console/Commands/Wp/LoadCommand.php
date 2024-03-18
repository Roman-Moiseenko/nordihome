<?php
declare(strict_types=1);

namespace App\Console\Commands\Wp;

use App\Entity\Photo;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use function public_path;

class LoadCommand extends Command
{
    private int $count_categories = 0;
    private int $count_products = 0;
    private string $storage;
    private Options $options;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wp:load
    {--type= : catalog / product / "пусто"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузка данных';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->storage = public_path() . '/temp/';
        $type = $this->option('type');
        $this->options = new Options(); //Настройки Магазина для товара

        if ($type == null) {
            $this->loadCatalog();
            $this->loadProduct();
        }
        if ($type == 'catalog') {
            $this->loadCatalog();
        }
        if ($type == 'product') {
            $this->loadProduct();
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

    private function create_product($data, $brand_id)
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
        $product->setPrice((float)$data['price']);
        //Изображения
        foreach ($data['images'] as $data_img) {
            $url = $data_img['url'];
            $alt = $data_img['alt'];
            //$upload_file_name = $this->copy_file($data_img);
            //$upload = new UploadedFile($this->storage . $upload_file_name, $upload_file_name, null, null, true);
            try {
                $sort = count($product->photos);
                $product->photo()->save(Photo::uploadByUrl($url, '', $sort, $alt));
                $product->refresh();
            } catch (\Throwable $e) {
                $this->error('Файл не загрузился ' . $url);
            }


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
        if (empty($parent)) {
            $result = Category::register($name);
        } else {
            $cat_parent = Category::where('name', $parent)->first();
            $result = Category::register($name, $cat_parent->id);
        }
        //Загрузка изображения
       // $upload_file_name = $this->copy_file($file);
       // $upload = new UploadedFile($this->storage . $upload_file_name, $upload_file_name, null, null, true);
        $result->image()->save(Photo::uploadByUrl($file, 'image'));
        //$result->image->newUploadFile($upload, 'image');
        $result->refresh();

        return $result->slug;
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
