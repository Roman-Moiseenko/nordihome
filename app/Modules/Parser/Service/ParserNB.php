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
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Service\ModificationService;
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
        return $array['props']['pageProps']['dehydratedState']['queries'];
    }


    public function parserCost(ProductParser $parser): float
    {
        $price_sell = $parser->price_sell;
        $url = $parser->product->brand->url . '/' . $parser->url;
        $data = $this->httpPage->getPage($url);
        $queries = $this->getQueries($data);
        $product = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['product']))
                $product = $query['state']['data']['product'];
        }
        if (empty($product)) return -1;
        $this->parserProductByData($product);

        $parser->refresh();
        if ($price_sell == $parser->price_sell) return 0;
        return $parser->price_sell;
    }

    public function availablePrice(string $code): bool
    {
        // TODO: Implement availablePrice() method.
    }

    protected function parserProductsByCategory(CategoryParser $categoryParser)
    {
        $domain = $categoryParser->brand->url;
        $url = $categoryParser->url;
        return $this->parserProductsByUrl($domain, $url);
    }

    private function parserProductsByUrl(string $domain, string $url, bool $is_first_page = true)
    {
        $url = $domain . '/' . $url;
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
                $this->parserProductsByUrl($url . '?page=' . $i, false)
            );
        }

        return $products;
     //   dd($products);
        //TODO Убрать когда заработает через axios

        foreach ($products as $product) {
            //dd($product);
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

    /**
     * Парсинг данных из фронтенда ()
     */
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
        $name = $this->translate->translate($product['name']); // GoogleTranslateForFree::translate('pl','ru', $product['name']);
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
        /** @var CategoryParser[] $parser_categories */
        for ($i = 0; $i < count($parser_categories); $i++) {
            if ($i == 0) $main_category = $parser_categories[$i]->category;
            if ($i > 0) $categories[] = $parser_categories[$i]->category_id;
        }

        //Парсим общие атрибуты
        $attributes = $main_category->all_attributes();

        $attribute_data = $this->parseAttributes($product['properties'], $attributes);

        //Размер распарсиваем отдельно, для создания новых товаров из Модификации
        $attr_size = null;
        foreach ($attributes as $attribute) {
            if ($attribute->name == 'Размер') $attr_size = $attribute;
        }
        $_products = []; //Массив вариантов для Модификации
        $data = [];
        //Если есть варианты
        foreach ($product['variants'] as $variant) {
            $code = $variant['warehouseSymbol']; //Артикул на основе модели и размера
            if ($code == null) continue;
            if (strpos($code, '.2E.')) continue;
            $size = null;
            foreach ($variant['options'] as $option) {
                if ($option['name'] == 'Rozmiar' || $option['groupId'] == '32') $size = $option['value'];
            }

            $_product = $this->createVariantProduct($variant, $attribute_data, $attr_size, $size, $name, $main_category, $categories, $data);
            $_products[] = $_product;
        }

        $modification = Modification::register($name, $_products[0]->id, [$attr_size]);
        //Парсинг Фото только для базового товара
        foreach ($image_urls as $image_url) {
            LoadingImageProduct::dispatch($_products[0], $image_url, $name, true);
        }
        $values = [];
        foreach ($_products as $_product) {
            //dd($_product->Value($attr_size->id));
            $values[$attr_size->id] = $_product->Value($attr_size->id);
            $modification->products()->attach($_product->id, ['values_json' => json_encode($values)]);
        }

        $product_parser = ProductParser::register($url, $_products[0]->id);
        $product_parser->maker_id = $maker_id;
        //$product_parser->model = $model;
        $product_parser->price_base = $price_base;
        $product_parser->price_sell = $price_sell;
        $product_parser->availability = true;
        $product_parser->data = $data;
        $product_parser->save();
        foreach ($parser_categories as $category) //Назначаем категории
            $product_parser->categories()->attach($category);

    }

    private function updateVariants(mixed $product): void
    {
        $maker_id = $product['id'];
        $price_base = $product['prices']['basePrice']['gross'];
        $price_sell = $product['prices']['sellPrice']['gross'];
        /** @var ProductParser $product_parser */
        $product_parser = ProductParser::where('maker_id', $maker_id)->first();
        $product_parser->price_base = $price_base;
        $product_parser->price_sell = $price_sell;
        $product_parser->save();

        $modification = $product_parser->product->modification;
        $main_category = $modification->base_product->category;
        $categories = $modification->base_product->categories;

        $attributes = $main_category->all_attributes();

        $attribute_data = $this->parseAttributes($product['properties'], $attributes);

        $attr_size = null;
        foreach ($modification->prod_attributes as $attribute) {
            if ($attribute->name == 'Размер') $attr_size = $attribute;
        }

        foreach ($product['variants'] as $variant) {
            $code = $variant['warehouseSymbol']; //Артикул на основе модели и размера
            if ($code == null) continue;
            $availability = $variant['availability']['buyable'];

            $size = null;
            foreach ($variant['options'] as $option) {
                if ($option['name'] == 'Rozmiar' || $option['groupId'] == '32') $size = $option['value'];
            }

            if (is_null($product = $this->findByValueInModification($modification, $size, $attr_size))) {
                $data = $product_parser->data;
                $product = $this->createVariantProduct($variant, $attribute_data, $attr_size, $size, $modification->name, $main_category, $categories, $data);
                $product_parser->data = $data;
                $product_parser->save();
                $values[$attr_size->id] = $product->Value($attr_size->id);
                $modification->products()->attach($product->id, ['values_json' => json_encode($values)]);
                $product->setPublished();

            }
            $product->not_sale = !$availability;
            $product->save();
            $product->refresh();
            //TODO Вынести
            // Изменение базового продукта, если он недоступен
            if (!$availability) {
                //Базовый в модификации стал не доступен
                if (!is_null($product->main_modification)) {
                    foreach ($product->modification->products as $_product) {
                        if ($_product->isSale()) {
                            /** @var ModificationService $service */
                            $service = app()->make(ModificationService::class);
                            $service->setBase($product->modification, $_product->id);
                            return;
                        }
                    }
                }
            } else {
                //Стал доступен один из модификации, когда вся модификация недоступна
                if (!$product->modification->isSale()) {
                    //И текущий не базовый
                    if (!is_null($product->main_modification)) {
                        /** @var ModificationService $service */
                        $service = app()->make(ModificationService::class);
                        $service->setBase($product->modification, $product->id);
                        return;
                    }
                }

            }
        }
    }

    private function findByValueInModification(Modification $modification, $size, Attribute $attr_size)
    {
        foreach ($modification->products as $product) {
            $values = json_decode($product->pivot->values_json, true);
            $id_variant = $values[(string)$attr_size->id];
            $variant = AttributeVariant::find($id_variant);
            if ($variant->name == $size) return $product;
        }
        return null;
    }

    private function createVariantProduct(
        $variant,
        $attribute_data, $attr_size, $size,
        $name,
        $main_category, $categories,
        &$data): Product
    {
        $ean = $variant['ean'];
        $code = $variant['warehouseSymbol'];

        if (!$attr_size->isValue($size)) {
            $attr_size->addVariant($size);
            $attr_size->refresh();
        }//Если такого размера нет, то добавляем
        $variant_size = $attr_size->findVariant($size)->id;

        $price_base_v = $variant['prices']['basePrice']['gross'];
        $price_sell_v = $variant['prices']['sellPrice']['gross'];
        $availability = $variant['availability']['buyable'];
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
        //$_product->model = $model;
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
        //Атрибуты общие
        foreach ($attribute_data as $datum) {
            $_product->prod_attributes()->attach($datum['attribute']->id, ['value' => json_encode($datum['variant'])]);
        }
        //$_product->prod_attributes()->attach($attr_color->id, ['value' => json_encode($variant_color)]);
        //Атрибут размера
        $_product->prod_attributes()->attach($attr_size->id, ['value' => json_encode($variant_size)]);
        $_product->refresh();
        return $_product;
    }

    private function parseAttributes(array $properties, $attributes): array
    {
        $array = [];
        //Заносим список значений в массив
        foreach ($properties as $property) {
            $dataList = $property['value']['dataList'];
            if (!empty($dataList)) {
                if ($property['name'] == 'Model') {
                    $model = trim($dataList[0]);
                    preg_match_all('/\d/', $model, $res);
                    $result = implode($res[0]);
                    $array['Серия'] = ['value' => $result];
                    $array['Модель'] = ['value' => $model];
                }
                if ($property['name'] == 'Kolor') $array['Цвет'] = ['value' => trim($dataList[0])];
                if ($property['name'] == 'Dla kogo') $array['Для кого'] = ['value' => $dataList,];
                if ($property['name'] == 'Płeć') $array['Пол'] = ['value' => $dataList,];
                if ($property['name'] == 'Kategoria') $array['Категория'] = ['value' => $dataList[0]];
                if ($property['name'] == 'Drop') $array['Подошва'] = ['value' => $dataList[0]];
            }
            //Одежда
            // Szerokość - Ширина
            // Produkt ocieplany - Утепленный продукт
            // Rodzaj zapięcia - Тип застежки

        }
        //Каждому типу значений находим соответствующий атрибут
        foreach ($attributes as $attribute) {
            foreach ($array as $key => $value) {
                if ($attribute->name == $key)
                    $array[$key]['attribute'] = $attribute;
            }

        }
        //Для каждого значения value находим id его Варианта (если не нашли, создаем)
        foreach ($array as $key => $item) {
            $value = $item['value'];
            /** @var Attribute $attribute */
            $attribute = $item['attribute'];
            $variant = null;
            if ($attribute->isVariant()) { //Если атрибут Вариативный ищем id
                if (is_array($value)) {
                    foreach ($value as $_v) {
                        $variant[] = $this->ValueToVariant($attribute, $_v);
                    }
                } else {
                    $variant = $this->ValueToVariant($attribute, $value);
                }
            } else { //Если нет (строка), то присваиваем само значение
                $variant = $value;
            }

            $array[$key]['variant'] = $variant;
        }

        return $array;
    }

    private function ValueToVariant(Attribute $attribute, string $value): int
    {
        $value_ru = $this->translate->translate($value);
        if (!$attribute->isValue($value_ru)) {
            $attribute->addVariant($value_ru);
            $attribute->refresh();
        }
        return $attribute->findVariant($value_ru)->id;
    }




    public function findProduct(string $search):? Product
    {
        // TODO: Implement findProduct() method.
    }

    public function remainsProduct(string $code): float
    {
        // TODO: Implement remainsProduct() method.
    }
}

