<?php
declare(strict_types=1);

namespace App\Modules\Parser\Service;

use App\Jobs\LoadingImageProduct;
use App\Modules\Base\Entity\Package;
use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Log;

class ParserIkea extends ParserAbstract
{

    protected string $brand_name = 'Икеа';

    const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/meta-data/navigation/catalog-products-slim.json?cb=2dy1g6t4pz';
    const string API_URL_PRODUCTS = 'https://sik.search.blue.cdtapps.com/pl/pl/product-list-page/more-products?category=%s&start=%s&end=%s';
    const string API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';

    public function parserCategories(): ?string
    {
        set_time_limit(1000);
        $data = $this->httpPage->getPage(self::API_URL_CATEGORIES);
        $categories = json_decode($data, true);
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
        set_time_limit(30);
        return $data;
    }

    private function addCategory($category, $parent_parser = null): void
    {
        $url = $category['id'] ?? '';
        if (is_null($cat_parser = CategoryParser::where('url', $url)->first())) {
            try {
                $name = GoogleTranslateForFree::translate('pl', 'ru', $category['name']);
            } catch (\Throwable $e) {
                $name = $category['name'];
            }

            $cat_parser = $this->categoryParserService->create($name, $url, $parent_parser);
            $cat_parser->brand_id = $this->brand->id;
            $cat_parser->save();
            //Пока не дублируем

        }
        //Дочерние категории
        if (isset($category['subs']))
            foreach ($category['subs'] as $child) {
                $this->addCategory($child, $cat_parser->id);
            }
    }


    protected function parserProductsByUrl(string $domain, string $url): array
    {
        $category = $url;
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $category, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list =  $_array['moreProducts']['productWindow'];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);
       // return $products;
        // dd($products);
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

    public function parserProductByData(array $product): void
    {
        // TODO: Implement parserProductByData() method.
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

    private function updateVariants(mixed $product)
    {
    }

    private function createProduct(mixed $product): void
    {
        $maker_id = $product['id'];
        $name = $product['name'] . ' ' . $product['typeName'];
        $url = $product['pipUrl'];
        $code = $product['itemNoGlobal'];
        $price_sell = $product['salesPrice']['numeral'];
        $price_base = $price_sell;
        $image = $product['mainImageUrl'] ?? '';

        $name = $this->translate->translate($name);

        //Проверяем, была ли предыдущая цена.
        if (isset($product['salesPrice']['lowestPreviousSalesPrice'])) {
            $price_base = (float)(str_replace(' ', '', $product['salesPrice']['lowestPreviousSalesPrice']['wholeNumber']) . '.' . $product['salesPrice']['lowestPreviousSalesPrice']['decimals']);
            if ($price_base > (float)$price_sell) $price_sell = $price_base;
        }
        $color = [];
        if (isset($product['colors'])) $color = array_map(function ($item) { return $item['name'];}, $product['colors']);
        $_categories = array_map(function ($item) { return $item['key'];}, $product['categoryPath']);


        $parser_categories = array_filter(array_map(function ($item) {
            return CategoryParser::where('brand_id', $this->brand->id)->where('url', $item)->where('active', true)->first();
        }, $_categories));

        /** @var CategoryParser[] $parser_categories */
        $main_category = $parser_categories[count($parser_categories) - 1]->category; //Последняя главная

//        dd($parser_categories);

        $data = $this->parsingDataByUrl($url);

        //Создаем товар
        $_product = Product::register($name, $this->toCode($code), $main_category->id);

        //$_product->model = $model;
        $_product->barcode = '';
        $_product->name_print = $_product->name;
        $_product->local = true;
        $_product->delivery = true;

        $_product->brand_id = $this->brand->id;
        $_product->country_id = Country::where('name', 'Польша')->first()->id;
        $_product->vat_id = VAT::where('value', null)->first()->id;
        $_product->measuring_id = Measuring::where('name', 'шт')->first()->id;
        //$_product->marking_type_id = MarkingType::whereRaw("LOWER(name) like '%одежда%'")->first()->id;

        $_product->short = $_product['description'];
        foreach ($_product['packages'] as $item) {
            $product->packages->add($item);
        }

        $product->save();


        //Проверяем есть ли товары в составе
        foreach ($_product['composite'] as $composite) {
            $_prod = $this->findProduct($composite['code']);
            $product->composites()->attach($_prod, ['quantity' => $composite['quantity']]);
        }


        $_product->save();

        //Создаем парсер товара

        $product_parser = ProductParser::register($url, $_product->id);
        $product_parser->maker_id = $maker_id;
        //$product_parser->model = $model;
        $product_parser->price_base = $price_base;
        $product_parser->price_sell = $price_sell;
        $product_parser->data = $data;
        $product_parser->save();
        foreach ($parser_categories as $category) //Назначаем категории
            $product_parser->categories()->attach($category);

        dd(1);
        //Парсинг Фото
        LoadingImageProduct::dispatch($_product, $image, $name, true); //Главное фото
        foreach ($data['images'] as $image_url) {
            LoadingImageProduct::dispatch($_product, $image_url, $name, true);
        }

      //  dd([$image, $data['images']]);
    }

    public function parsingData(string $code)
    {
        $url = sprintf(self::API_URL_PRODUCT, $code); //API для поиска товара
        $json_product = $this->httpPage->getPage($url, '_cache');
        if ($json_product == null) {
            Log::error('Икеа Парсинг ' . $code . ' null');
            return null;
        }
        $_array = json_decode($json_product, true);

        if ($_array == null){
            Log::error('Икеа Парсинг ' . $code . ' null');
            return null;
        }
        if (empty($_array['searchResultPage']['products']['main']['items']))
        {
            Log::error('Икеа Парсинг ' . $code . ' empty($_array[searchResultPage][products][main][items])');
            return null;
        }
        $item = $_array['searchResultPage']['products']['main']['items'][0]['product'];
        //TODO Вынести отдельно

        //Парсим первычный JSON
        $name = $item['name'] . ' ' . $item['typeName']; ;
        $link = $item['pipUrl'];
        $image = $item['mainImageUrl'] ?? '';
        $price = $item['salesPrice']['numeral'];

        //Проверяем, была ли предыдущая цена.
        if (isset($item['salesPrice']['lowestPreviousSalesPrice'])) {
            $_previous = (float)(str_replace(' ', '', $item['salesPrice']['lowestPreviousSalesPrice']['wholeNumber']) . '.' . $item['salesPrice']['lowestPreviousSalesPrice']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }

        $color = [];
        if (isset($product['colors'])) $color = array_map(function ($item) { return $item['name'];}, $product['colors']);
        $_categories = array_map(function ($item) { return $item['key'];}, $product['categoryPath']);


        $parser_categories = array_filter(array_map(function ($item) {
            return CategoryParser::where('brand_id', $this->brand->id)->where('url', $item)->where('active', true)->first();
        }, $_categories));

        /** @var CategoryParser[] $parser_categories */
        $main_category = $parser_categories[count($parser_categories) - 1]->category; //Последняя главная


        return $this->parsingDataByUrl($link);
    }

    public function parsingDataByUrl(string $url): array|null
    {
        //Сканируем страницу для остальных параметров
        $pageProduct = $this->httpPage->getPage($url);
        preg_match_all('#data-hydration-props="(.+?)"#su', $pageProduct, $res);
        $_res = $res[1][0];
        $_res = str_replace('&quot;', '"', $_res);
        $_data = json_decode($_res, true);

        ////Определяем есть ли составные артикулы
        $_sub = $_data['stockcheckSection']['subProducts']; //availabilityHeaderSection

        $composite = [];
        if (count($_sub) != 0) {
            foreach ($_sub as $_item) {
                $composite[] = [
                    'code' => $this->toCode($_item['itemNo']),
                    'quantity' => $_item['quantity'],
                ];
            }
        }
        $pack = $_data['stockcheckSection']['numberOfPackages']; //Кол-во пачек
        $packages = [];
        $_packages = $_data['stockcheckSection']['packagingProps']['packages'];
        foreach ($_packages as $_item) {
            $_quantity = $_item['quantity']['value']; //кол-во элементов в пачке данного товара
            if (count($_item['measurements']) != 0) //Пропускаем для самого товара, только по составным
                foreach ($_item['measurements'] as $measurement) { //Если товар в 1 пачке разбит на несколько
                    $packages[] = Package::create(
                        $this->toHeight($measurement),
                        $this->toWidth($measurement),
                        $this->toLength($measurement),
                        $this->toWeight($measurement),
                        $_quantity,
                    );
                }
        }
        ////Описание и перевод
        $description = $_data['pipPricePackage']['productDescription'] .
            (empty($_data['pipPricePackage']['measurementText']) ? '' : ', ' . $_data['pipPricePackage']['measurementText']);

        $tr = new GoogleTranslateForFree();
        $description = $tr->translate('pl', 'ru', $description, 5);
        $images = [];
        $_list_images = $_data['productGallery']['mediaList']; //$_data['mediaGrid']['fullMediaList']
        foreach ($_list_images as $item) {
            if ($item['type'] == 'image' && $item['content']['type'] != 'MAIN_PRODUCT_IMAGE')
                $images[] = $item['content']['url'];
        }
       // return $images;

        return [
            'description' => $description,
            'images' => $images,
            'packages' => $packages,
            'pack' => $pack,
            'composite' => $composite,
        ];
    }

    private function toWeight(array $_measures)
    {
        $weight = 0.0;
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "weight") $weight = $_measure['value'];
        }
        return $weight;
    }

    private function toHeight(array $_measures)
    {
        $height = 0.0;
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "height") $height = $_measure['value'];
        }
        if ($height == 0.0) $height = $this->fromDiameter($_measures);
        return $height;
    }

    private function fromDiameter(array $_measures)
    {
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "diameter") return $_measure['value'];
        }
        return 0.0;
    }

    private function toLength(array $_measures)
    {
        $length = 0.0;
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "length") $length = $_measure['value'];
        }
        if ($length == 0.0) $length = $this->fromDiameter($_measures);

        return $length;
    }

    private function toWidth(array $_measures)
    {
        $width = 0.0;
        foreach ($_measures as $_measure) {
            if ($_measure['type'] == "width") $width = $_measure['value'];
        }
        if ($width == 0.0) $width = $this->fromDiameter($_measures);

        return $width;
    }

    public function toCode(string $code): string
    {
        if (empty($code)) return '';
        $code = substr_replace($code, '.', 6, 0);
        return substr_replace($code, '.', 3, 0);
    }
}
