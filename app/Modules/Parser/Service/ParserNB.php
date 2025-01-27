<?php

namespace App\Modules\Parser\Service;

use App\Jobs\LoadingImageProduct;
use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\NBRussia\Helper\MenuListing;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;

class ParserNB extends ParserAbstract
{

    protected string $brand_name = 'New Balance';


    public function parserCategories(): array
    {
        set_time_limit(1000);
        /*$data = $this->httpPage->getPage($this->brand->url);
        $queries = $this->getQueries($data);
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }
        */
        $menu = MenuListing::categories();
        //dd($menu);

        $children = $menu['children'];
        foreach ($children as $child) {
            $this->addCategory($child);
        }
        set_time_limit(30);
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

        return $products;
        //dd($products);
        //TODO Убрать когда заработает через axios
        foreach ($products as $product) {
            //Ищем товар в базе по Id
            $parser_product = ProductParser::where('maker_id', $product['id'])->first();
            //Если есть .... Надо проверить модификации (варианты) либо добавить, либо убрать из продажи
            if (!is_null($parser_product)) {
                $this->updateVariants($product);
            } else {
                $this->createProduct($product);
            }

        }
        return $products;
    }

    public function parserProductByData(array $product): void
    {
        $parser_product = ProductParser::where('maker_id', $product['id'])->first();
        //Если есть .... Надо проверить модификации (варианты) либо добавить, либо убрать из продажи
        if (!is_null($parser_product)) {
            $this->updateVariants($product);
        } else {
            \DB::transaction(function () use ($product) {
                $this->createProduct($product);
            });

        }
    }



    private function createProduct(mixed $product): void
    {
        //Создаем массив данных для добавления. Название, Модель, Изображения
        $maker_id = $product['id'];
        $name = GoogleTranslateForFree::translate('pl','ru', $product['name']);
        $image_urls = array_map(function ($item) {
            return $this->brand->url . '/picture' . str_replace('{imageSafeUri}', '', $item);
        }, $product['pictures']);
        $url = $product['niceUrl'];
        $price_base = $product['prices']['basePrice']['gross'];
        $price_sell = $product['prices']['sellPrice']['gross'];

        $parser_categories = array_filter(array_map(function ($item) {
            return CategoryParser::where('brand_id', $this->brand->id)->where('url', $item['niceUrl'])->where('active', true)->first();
        }, $product['categories']));

        $main_category = null;
        $categories = [];
        /** @var CategoryParser[]  $parser_categories */
        for($i = 0; $i < count($parser_categories); $i++) {
            if ($i == 0) $main_category = $parser_categories[$i]->category;
            if ($i > 0) $categories[] = $parser_categories[$i]->category_id;
        }
        $model = '';
        $color = '';
        foreach ($product['properties'] as $property) {
            if ($property['name'] == 'Model') $model = trim($property['value']['dataList'][0]);
            if ($property['name'] == 'Kolor') $color = $property['value']['dataList'][0];
        }

        $attributes = $main_category->all_attributes();

        $attr_size = null;
        $attr_color = null;

        foreach ($attributes as $attribute) {
            if ($attribute->name == 'Размер') $attr_size = $attribute;
            if ($attribute->name == 'Цвет') $attr_color = $attribute;
        }
        //TODO Для цветов и других атрибутов сделать таблицу перевода world (unique), russia
        $color_ru = GoogleTranslateForFree::translate('pl','ru', $color);
        if (!$attr_color->isValue($color_ru)) {
            $attr_color->addVariant($color_ru);
            $attr_color->refresh();
        }
        $variant_color = $attr_color->findVariant($color_ru)->id;

        $_products = []; //Массив вариантов для Модификации
        $data = [];
        //Если есть варианты
        foreach ($product['variants'] as $variant) {
            $ean = $variant['ean'];
            $code = $variant['warehouseSymbol']; //Артикул на основе модели и размера
            if ($code == null) continue;

            $price_base_v = $variant['prices']['basePrice']['gross'];
            $price_sell_v = $variant['prices']['sellPrice']['gross'];
            $availability = $variant['availability']['buyable'];

            $size = null;
            foreach ($variant['options'] as $option) {
                if ($option['name'] == 'Rozmiar' || $option['groupId'] == '32') $size = $option['value'];
            }
            if (!$attr_size->isValue($size)) {
                $attr_size->addVariant($size);
                $attr_size->refresh();
            }//Если такого размера нет, то добавляем
            $variant_size = $attr_size->findVariant($size)->id;

            $data[$size] = [
                'price_base' => $price_base_v,
                'price_sell' => $price_sell_v,
                'availability' => $availability,
            ];
            //К названию добавляем Размер, ищем и назначаем атрибут, остальные данные дублируем
            $name_v = $name . ' ' . $size;

            $_product = Product::register($name_v, $code, $main_category->id);
            foreach ($categories as $category) {//Назначаем категории
                $_product->categories()->attach($category);
            }
            $_product->not_sale = !$availability;
            $_product->model = $model;
            $_product->barcode = $ean ?? '';
            $_product->name_print = $_product->name;
            $_product->local = true;
            $_product->delivery = true;

            $_product->brand_id = $this->brand->id;
            $_product->country_id = Country::where('name', 'Польша')->first()->id;
            $_product->vat_id = VAT::where('value', null)->first()->id;
            $_product->measuring_id = Measuring::where('name', 'шт')->first()->id;
            $_product->marking_type_id = MarkingType::whereRaw("LOWER(name) like '%одежда%'")->first()->id;

            $_product->save();
            //Аттрибуты
            $_product->prod_attributes()->attach($attr_color->id, ['value' => json_encode($variant_color)]);
            $_product->prod_attributes()->attach($attr_size->id, ['value' => json_encode($variant_size)]);

            //задание на парсинг Фото товара
            //dd($image_urls);
            foreach ($image_urls as $image_url) {
                LoadingImageProduct::dispatch($_product, $image_url, $name_v, true);
            }

            $_products[] = $_product;
        }

        $modification = Modification::register($name, $_products[0]->id, [$attr_size]);
        $values = [];
        foreach ($_products as $_product) {
            //dd($_product->Value($attr_size->id));
            $values[$attr_size->id] = $_product->Value($attr_size->id);
            $modification->products()->attach($_product->id, ['values_json' => json_encode($values)]);
        }

        $product_parser = ProductParser::register($url, $_products[0]->id);
        $product_parser->maker_id = $maker_id;
        $product_parser->model = $model;
        $product_parser->price_base = $price_base;
        $product_parser->price_sell = $price_sell;
        $product_parser->data = $data;
        $product_parser->save();
        foreach ($parser_categories as $category) //Назначаем категории
            $product_parser->categories()->attach($category);

    }

    private function updateVariants(mixed $product)
    {


    }
}
