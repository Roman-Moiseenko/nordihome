<?php

namespace App\Modules\Parser\Service;

use App\Jobs\LoadingImageProduct;
use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;

class ParserNB extends ParserAbstract
{

    protected string $brand_name = 'NB';


    public function parserCategories()
    {
        $data = $this->httpPage->getPage($this->brand->url);
        $queries = $this->getQueries($data);
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }

        $children = $menu['children'];
        foreach ($children as $child) {
            $this->addCategory($child);
        }
        return $menu;
    }

    private function addCategory($category, $parent_parser = null, $parent = null): void
    {
        //Есть ли у категории ссылка
        $url = $category['href'] ?? '';
        if (empty($url)) $url = $category['niceUrl'] ?? '';
        if (empty($url)) return;
        //Если категория еще не парсилась
        if (is_null($cat_parser = CategoryParser::where('url', $url)->first())) {
            //Создаем новую категорию парсера
            $name = GoogleTranslateForFree::translate('pl', 'ru', $category['title']);
            $cat_parser = $this->categoryParserService->create($name, $url, $parent_parser);
            //Дублируем категорию в список Категорий магазина
            $cat = Category::register(
                $name,
                $parent,
            );
            $cat_parser->brand_id = $this->brand->id;
            $cat_parser->category_id = $cat->id;
            $cat_parser->save();
        }
        //Дочерние категории
        foreach ($category['children'] as $child) {
            $this->addCategory($child, $cat_parser->id, $cat_parser->category->id);
        }
        //Создать категорию
    }


    private function getQueries(string $data)
    {
        $begin = strpos($data, '<script id="__NEXT_DATA__" type="application/json">');
        $text = substr($data, $begin + strlen('<script id="__NEXT_DATA__" type="application/json">'));
        $end = strpos($text, '</script>');
        $newData = substr($text, 0, $end);
        $array = json_decode($newData, true);
        $queries = $array['props']['pageProps']['dehydratedState']['queries'];
        return $queries;
    }

    public function parserProducts(?int $category_id)
    {

    }


    public function findProduct(string $search): Product
    {
        // TODO: Implement findProduct() method.
    }

    public function remainsProduct(string $code): float
    {
        // TODO: Implement remainsProduct() method.
    }

    public function costProduct(string $code): float
    {
        // TODO: Implement costProduct() method.
    }

    public function availablePrice(string $code): bool
    {
        // TODO: Implement availablePrice() method.
    }


    protected function parserProductsByUrl(string $url, bool $is_first_page = true)
    {
        $data = $this->httpPage->getPage($url);
        $queries = $this->getQueries($data);
        $data = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['products']))
                $data = $query['state']['data']['products'];
        }
        $products = $data['items'];
        if (!$is_first_page) return $products; //Уже обрабатывается пагинация
        $pagination = $data['pagination']['lastPage'];
        for ($i = 2; $i <= $pagination; $i++) {
            $products = array_merge(
                $products,
                $this->parserProductsByUrl($url . '?page=' .$i, false)
            );
        }


       // dd($products[0]);

        foreach ($products as $product) {
            //Ищем товар в базе по Id
            $parser_product = ProductParser::where('maker_id', $product['id'])->first();
            //Если есть .... Надо проверить модификации (варианты) либо добавить, либо убрать из продажи
            if (!is_null($parser_product)) {
                $this->updateVariants($product);
            } else {
                $this->createProduct($product);
            }

            //Если не нашли
        }

    }

    private function createProduct(mixed $product)
    {
        //Создаем массив данных для добавления. Название, Модель, Изображения
        $id = $product['id'];
        $name = GoogleTranslateForFree::translate('pl','ru', $product['name']);
        $image_urls = array_map(function ($item) {
            return $this->brand->url . '/picture/' . str_replace('{imageSafeUri}', '', $item);
        }, $product['pictures']);
        $url = $product['niceUrl'];
        $price_base = $product['prices']['basePrice']['gross'];
        $price_sell = $product['prices']['sellPrice']['gross'];

        $parser_categories = array_map(function ($item) {
            return Category::where('brand_id', $this->brand->id)->where('url', $item['niceUrl'])->first();
        }, $product['categories']);

        $main_category_id = null;
        $categories = [];
        /** @var CategoryParser[]  $parser_categories */
        for($i = 0; $i < count($parser_categories); $i++) {
            if ($i == 0) $main_category_id = $parser_categories[$i]->category_id;
            if ($i > 0) $categories[] = $parser_categories[$i]->category_id;
        }
        $model = '';
        $color = [];
        foreach ($product['properties'] as $property) {
            if ($property['name'] == 'Model') $model = trim($property['value']['dataList'][0]);
            if ($property['name'] == 'Kolor') $color = trim($property['value']['dataList']);
        }

        $_products = []; //Массив вариантов для Модификации
        //Если есть варианты
        foreach ($product['variants'] as $variant) {
            $ean = $variant['ean'];
            $code = $variant['warehouseSymbol']; //Артикул на основе модели и размера
            $price_base_v = $variant['prices']['basePrice']['gross'];
            $price_sell_v = $variant['prices']['sellPrice']['gross'];
            $availability = $variant['availability']['buyable'];
            //TODO Создавать если $availability ?? Проверить наличие на сайте
            $size = null;
            foreach ($variant['options'] as $option) {
                if ($option['name'] == 'Rozmiar' || $option['groupId'] == '32') $size = $option['value'];
            }
            //К названию добавляем Размер, ищем и назначаем атрибут, остальные данные дублируем
            $name_v = $name . ' ' . $size;

            $_product = Product::register($name_v, $code, $main_category_id);
            foreach ($categories as $category) {//Назначаем категории
                $_product->categories()->attach($category);
            }
            $_product->not_sale = !$availability;
            $_product->model = $model;
            $_product->save();

            foreach ($image_urls as $image_url) {
                LoadingImageProduct::dispatch($_product, $image_url, $name_v);
            }
            //Аттрибуты
            //Размер
            $size_attr = Attribute::where('name', 'Размер')->whereIn(''); //Отбор по категории
            //Цвет
            
            //Заполняем остальные данные, задание на парсинг Фото товара


            $_products[] = $_product;
        }
        ProductParser::register($name, $url, $_products[0]->id);
        //Создаем парсер-товар, ссылка на первый размер

        //Заполняем данные, привязываем категории

    }

    private function updateVariants(mixed $product)
    {


    }
}
