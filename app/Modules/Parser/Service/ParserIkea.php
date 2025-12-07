<?php
declare(strict_types=1);

namespace App\Modules\Parser\Service;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Package;
use App\Modules\Base\Job\LoadingImageCatalog;
use App\Modules\Base\Job\LoadingImageProduct;
use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Base\Service\YandexTranslate;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ParserLogItem;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Job\CreateParserProduct;
use App\Modules\Parser\Job\ParserCategory;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Parser;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Deprecated;

class ParserIkea extends ParserAbstract
{

    protected string $brand_name = 'Икеа';

    const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/meta-data/navigation/catalog-products-slim.json?cb=2dy1g6t4pz';
    const string API_URL_PRODUCTS = 'https://sik.search.blue.cdtapps.com/pl/pl/product-list-page/more-products?category=%s&start=%s&end=%s';
    const string API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';

    const string API_URL_QUANTITY = 'https://api.ingka.ikea.com/cia/availabilities/ru/pl?itemNos=%s&expand=StoresList,Restocks,SalesLocations,DisplayLocations,ChildItems,FoodAvailabilities';
    private ParserLogService $logService;

    public function __construct(        CategoryParserService $categoryParserService,
                                        TranslateService      $translate, ParserLogService $logService)
    {
        parent::__construct($categoryParserService, $translate);

        $this->logService = $logService;
    }

    /**
     * Процедура для Job - На Икеа получаем список всех корневых категорий для запуска Job по их созданию (addProduct)
     * @return array
     */
    public function parserCategoriesJob(): array
    {
        set_time_limit(1000);
        $data = $this->httpPage->getPage(self::API_URL_CATEGORIES);
        set_time_limit(30);
        return json_decode($data, true);
    }


   /* public function parserProductsByCategoryJob(CategoryParser $categoryParser): void
    {
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $categoryParser->url, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list = $_array['moreProducts']['productWindow'];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);
        foreach ($products as $product) {
            $product['parser_category_id'] = $categoryParser->id;
            CreateParserProduct::dispatch($product);
        }
    }

    */
    /**
    * Процедура для Job - по урл категории Икеа, получаем весь список товаров для запуска Job по их созданию
    */
    public function getProductsByCategoryJob(string $url): array
    {
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $url, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list = $_array['moreProducts']['productWindow'];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);
        return $products;
    }


    public function createProductJob(array $product_data): Product
    {
        Log::debug('ParserIkea->createProductJob: Начало');
        $maker_id = $product_data['id'];

        $name = $product_data['name'] . ' ' . $product_data['typeName'];
        $url = $product_data['pipUrl'];
        $code = $product_data['itemNoGlobal'];
        $price_sell = $product_data['salesPrice']['numeral'];
        $price_base = $price_sell;
        $image = $product_data['mainImageUrl'] ?? '';

        $name = $this->translate->translate($name);

        //Проверяем, была ли предыдущая цена.
        if (isset($product_data['salesPrice']['lowestPreviousSalesPrice'])) {
            $price_base = (float)(str_replace(' ', '', $product_data['salesPrice']['lowestPreviousSalesPrice']['wholeNumber']) . '.' . $product_data['salesPrice']['lowestPreviousSalesPrice']['decimals']);
            if ($price_base > (float)$price_sell) $price_sell = $price_base;
        }

        //Создаем товар, если его нет в базе
        if (is_null($product = Product::whereCode($code)->first())) {
            Log::debug('ParserIkea->createProductJob: Создаем товар - ' . $name);

            $data = $this->parsingDataByUrl($url);
            $main_category_id = Category::noParseCategory()->id;

            $product = Product::register($name, $this->toCode($code), $main_category_id);

            $product->barcode = '';
            $product->name_print = $product->name;
            $product->local = true;
            $product->delivery = true;
            $product->brand_id = $this->brand->id;
            $product->country_id = Country::where('name', 'Польша')->first()->id;

            $product->vat_id = Trader::default()->organization->vat_id; //Подставить от настроек текущего продавца
            //$product->vat_id = VAT::where('value', 5)->first()->id;
            $product->measuring_id = Measuring::where('name', 'шт')->first()->id;
            $product->short = $data['description'];
            foreach ($data['packages'] as $item) {
                $product->packages->add($item);
            }
            $product->save();

            //Проверяем есть ли товары в составе
            foreach ($data['composite'] as $composite) {
                Log::debug('ParserIkea->createProductJob: Есть композитные товары');
                $_prod = $this->findProduct($composite['code']);
                $product->composites()->attach($_prod, ['quantity' => $composite['quantity']]);
            }
            //Парсинг Фото
            LoadingImageProduct::dispatch($product, $image, $name, true); //Главное фото
            foreach ($data['images'] as $image_url) {
                LoadingImageProduct::dispatch($product, $image_url, $name, true);
            }
            //$product->setPublished();
        }
        //Создаем парсер товара
        if (is_null($product_parser = ProductParser::where('maker_id', $maker_id)->first())) {
            Log::debug('ParserIkea->createProductJob: Создаем Парсер товар - ' . $name);
            $product_parser = ProductParser::register($url, $product->id);
            $product_parser->maker_id = $maker_id;
            $product_parser->price_base = $price_base;
            $product_parser->price_sell = $price_sell;

            //цвет товара
            $colors = array_map(function ($item) {
                return $item['name'];
            }, $product_data['colors'] ?? []);

            $product_parser->data = [
                'colors' => $colors,
            ];
            //Данные о наличии
            $product_parser->save();


            if (isset($product_data['parser_category_id'])) $product_parser->categories()->attach($product_data['parser_category_id']);
            if (isset($product_data['categoryPath'])) {
                $code_categories = array_map(function ($item) {
                    return $item['key'];
                }, $product_data['categoryPath']);
                /** @var CategoryParser[] $parser_categories */
                $parser_categories = array_filter(array_map(function ($item) {
                    return CategoryParser::where('brand_id', $this->brand->id)->where('url', $item)->where('active', true)->first();
                }, $code_categories));

                foreach ($parser_categories as $category) //Назначаем категории
                    $product_parser->categories()->attach($category);
            }
            $this->logService->addLog($product_parser->id, ParserLogItem::STATUS_NEW);
        } else {
            $product_parser->product_id = $product->id;
            $product_parser->save();
        }
        $product_parser->refresh();

        Log::debug('ParserIkea->createProductJob: Товар полностью создан ' . $name);
        return $product;
    }

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

    public function addCategory($category, $parent_parser = null): void
    {
        $url = $category['id'] ?? '';
        if (is_null($cat_parser = CategoryParser::where('url', $url)->first())) {

            $name = $this->translate->translate($category['name']);

            $cat_parser = $this->categoryParserService->create($name, $url, $parent_parser);
            $cat_parser->brand_id = $this->brand->id;
            $cat_parser->save();

            $cat_parser->save();
            if (isset($category['im'])) {
                LoadingImageCatalog::dispatch($cat_parser, $category['im']);
            }
        }
        //Дочерние категории
        if (isset($category['subs']))
            foreach ($category['subs'] as $child) {
                $this->addCategory($child, $cat_parser->id); //TODO Возможно сделать через JOB ParserCategory::dispatch($child, $cat_parser->id)
            }
    }

    protected function parserProductsByCategory(CategoryParser $categoryParser): array
    {
        $code_category = $categoryParser->url;
        $main_category_id = $categoryParser->category_id;
        $parser_category = $categoryParser->id;
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $code_category, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list = $_array['moreProducts']['productWindow'];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);
        /*
                return array_map(function ($item) use ($main_category_id, $parser_category) {
                    $item['main_category_id'] = $main_category_id;
                    $item['parser_category_id'] = $parser_category;
                    return $item;

                }, $products);
                */
        // dd($products);
        //TODO Убрать когда заработает через axios

        set_time_limit(10000);
        foreach ($products as $product) {
            //dd($product);
            //Ищем товар в базе по Id
            $parser_product = ProductParser::where('maker_id', $product['id'])->first();
            //Если есть .... Надо проверить модификации (варианты) либо добавить, либо убрать из продажи
            if (!is_null($parser_product)) {
                $this->updateVariants($product);
            } else {
                $product['main_category_id'] = $main_category_id;
                $product['parser_category_id'] = $parser_category;
                \DB::transaction(function () use ($product) {
                    $this->createProduct($product);
                });
            }
        }
        set_time_limit(30);
        return $products;
    }

    #[Deprecated]
    private function createProduct(mixed $product_data): ?Product
    {
        $maker_id = $product_data['id'];
        $name = $product_data['name'] . ' ' . $product_data['typeName'];
        $url = $product_data['pipUrl'];
        $code = $product_data['itemNoGlobal'];
        $price_sell = $product_data['salesPrice']['numeral'];
        $price_base = $price_sell;
        $image = $product_data['mainImageUrl'] ?? '';

        $name = $this->translate->translate($name);

        //Проверяем, была ли предыдущая цена.
        if (isset($product_data['salesPrice']['lowestPreviousSalesPrice'])) {
            $price_base = (float)(str_replace(' ', '', $product_data['salesPrice']['lowestPreviousSalesPrice']['wholeNumber']) . '.' . $product_data['salesPrice']['lowestPreviousSalesPrice']['decimals']);
            if ($price_base > (float)$price_sell) $price_sell = $price_base;
        }
        $colors = [];
        if (isset($product_data['colors'])) $colors = array_map(function ($item) {
            return $item['name'];
        }, $product_data['colors']);
        $main_category_id = null;
        if (isset($product_data['main_category_id'])) {
            $main_category_id = $product_data['main_category_id'];
        } else {
            //TODO вытаскиваем последнюю categoryPath 'key'
            if (!isset($product_data['categoryPath'])) {
                $categories = [];
            } else {
                $categories = $product_data['categoryPath'];
            }
            if (count($categories) == 0) {
                $key = null;
            } else {
                $key = $categories[count($categories) - 1]['key'];
            }

            $category_parser = CategoryParser::where('url', $key)->first();
            if (!is_null($category_parser)) $main_category_id = $category_parser->category_id;

        }
        if (is_null($main_category_id)) {
            $main_category = Category::where('slug', 'temp')->first();
            if (is_null($main_category)) return null; //throw new \DomainException('Неверная главная категория');
            $main_category_id = $main_category->id;
        } //return /*null;*/

        $data = $this->parsingDataByUrl($url);
        //Создаем товар
        if (is_null($product = Product::whereCode($code)->first())) {
            //  Log::info('Создаем товар');
            $product = Product::register($name, $this->toCode($code), $main_category_id);
            $product->barcode = '';
            $product->name_print = $product->name;
            $product->local = true;
            $product->delivery = true;
            $product->brand_id = $this->brand->id;
            $product->country_id = Country::where('name', 'Польша')->first()->id;
            $product->vat_id = VAT::where('value', null)->first()->id;
            $product->measuring_id = Measuring::where('name', 'шт')->first()->id;
            $product->short = $data['description'];
            foreach ($data['packages'] as $item) {
                $product->packages->add($item);
            }
            $product->save();

            //Проверяем есть ли товары в составе
            foreach ($data['composite'] as $composite) {
                $_prod = $this->findProduct($composite['code']);
                $product->composites()->attach($_prod, ['quantity' => $composite['quantity']]);
            }
            //Парсинг Фото
            LoadingImageProduct::dispatch($product, $image, $name, true); //Главное фото
            foreach ($data['images'] as $image_url) {
                LoadingImageProduct::dispatch($product, $image_url, $name, true);
            }
        }
        //Создаем парсер товара
        if (is_null($product_parser = ProductParser::where('url', $url)->first())) {
            $product_parser = ProductParser::register($url, $product->id);
            $product_parser->maker_id = $maker_id;
            $product_parser->price_base = $price_base;
            $product_parser->price_sell = $price_sell;
            //Данные о наличии
            $product_parser->save();
            if (isset($product_data['parser_category_id'])) $product_parser->categories()->attach($product_data['parser_category_id']);
            if (isset($product_data['categoryPath'])) {
                $code_categories = array_map(function ($item) {
                    return $item['key'];
                }, $product_data['categoryPath']);
                /** @var CategoryParser[] $parser_categories */
                $parser_categories = array_filter(array_map(function ($item) {
                    return CategoryParser::where('brand_id', $this->brand->id)->where('url', $item)->where('active', true)->first();
                }, $code_categories));

                foreach ($parser_categories as $category) //Назначаем категории
                    $product_parser->categories()->attach($category);
            }
        } else {
            $product_parser->product_id = $product->id;
            $product_parser->save();
        }
        return $product;
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

    public function remainsProduct(string $code): float
    {
        // TODO: Implement remainsProduct() method.

        $url = sprintf(self::API_URL_QUANTITY, $code);
        $json_product = $this->httpPage->getPage($url, '_cache');

        $_array = json_decode($json_product, true);
        //dd($_array);
        $_result = [];
        if ($_array == null)
            throw new \DomainException('Неверный артикул или ссылка');
        foreach ($_array['availabilities'] as $item) {
            if (isset($item['availableForCashCarry'])) {
                $_store = (int)$item['classUnitKey']['classUnitCode']; //Номер склада
                //dd($item);
                if (isset($item['buyingOption']['cashCarry']['availability'])) {
                    $_quantity = (int)$item['buyingOption']['cashCarry']['availability']['quantity']; //Кол-во на складе
                } else {
                    $_quantity = 0;
                }
                if ($_store != 0) $_result[$_store] = $_quantity;
            }
        }

        dd($_result);
    }

    public function parserCost(ProductParser $parser): float|bool
    {
        $code = $parser->product->code;
        $url = sprintf(self::API_URL_PRODUCT, $code); //API для поиска товара
        $json_product = $this->httpPage->getPage($url, '_cache');
        $_array = json_decode($json_product, true);

        //Товар теперь недоступен
        if ($_array == null || empty($_array['searchResultPage']['products']['main']['items'])) {
            $parser->availability = false;
            $parser->save();
            $this->logService->addLog($parser->id, ParserLogItem::STATUS_DEL);
        }

        $item = $_array['searchResultPage']['products']['main']['items'][0]['product']['salesPrice'];
        $price = $item['numeral'];
        if (isset($item['previous'])) {
            $_previous = (float)(str_replace(' ', '', $item['previous']['wholeNumber']) . '.' . $item['previous']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }
        if ($parser->price_sell != $price) {
            $this->logService->addLog(
                $parser->id,
                ParserLogItem::STATUS_CHANGE,
                ['price_old' => $parser->price_sell, 'price_new' => $price]
            );
            $parser->price_sell = $price;
            $parser->save();
            return true;
        }
        return false;
    }

    public function availablePrice(string $code): bool
    {
        //return $this->parserCost($code) > 0;
    }

    private function updateVariants(mixed $product)
    {
    }

    public function findProduct(string $search): ?Product
    {
        $url = sprintf(self::API_URL_PRODUCT, $search); //API для поиска товара
        $json_product = $this->httpPage->getPage($url, '_cache');
        if ($json_product == null) {
            Log::error('Икеа Парсинг ' . $search . ' null');
            return null;
        }
        $_array = json_decode($json_product, true);
        //  Log::info($json_product);
        if ($_array == null) {
            Log::error('Икеа Парсинг ' . $search . ' null');
            return null;
        }
        if (empty($_array['searchResultPage']['products']['main']['items'])) {
            Log::error('Икеа Парсинг ' . $search . ' товар не найден');
            return null;
        }

        $item = $_array['searchResultPage']['products']['main']['items'][0]['product'];
        return $this->createProductJob($item);
    }

    public function parsingDataByUrlOld(string $url): array|null
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

        return [
            'description' => $description,
            'images' => $images,
            'packages' => $packages,
            'pack' => $pack,
            'composite' => $composite,
        ];
    }

    public function parsingDataByUrl(string $url): array|null
    {
        $pageProduct = $this->httpPage->getPage($url);
        preg_match_all('#<script type="text\/hydrate">(.+?)<\/script>#su', $pageProduct, $res);
        $_res = $res[1][0];
        $_data = json_decode($_res, true);
        $dataProduct = $_data["pageProps"]["clientProduct"];
        //Составные товары
        $composite = array_map(function ($subProduct) {
            return [
                'code' => $this->toCode($subProduct['itemNo']),
                'quantity' => $subProduct['quantity'],
            ];
        }, $dataProduct['subProducts'] ?? []);

        //Пачки товара
        $packaging = $dataProduct['packaging'];
        $pack = $packaging['numberOfPackages'];
        $_packages = $packaging['packages'];
        $packages = [];
        foreach ($_packages as $_package) {
            if(!empty($measurements = $_package['measurements'])) {
                $_quantity = $_package['quantity']['value'];
                foreach ($measurements as $measurement) { //Если товар в 1 пачке разбит на несколько
                    $packages[] = Package::create(
                        $this->toHeight($measurement),
                        $this->toWidth($measurement),
                        $this->toLength($measurement),
                        $this->toWeight($measurement),
                        $_quantity,
                    );
                }
            }
        }

        $description = $dataProduct['description'] .
            (empty($dataProduct['itemMeasureReferenceText']) ? '' : ', ' . $dataProduct['itemMeasureReferenceText']);
        $description = $this->translate->translate($description);

        $images = [];
        $_list_images = $dataProduct['mediaList']; //$_data['mediaGrid']['fullMediaList']
        foreach ($_list_images as $item) {
            if ($item['type'] == 'image' /* && $item['content']['type'] != 'MAIN_PRODUCT_IMAGE'*/ ) //Возможно разблокировать
                $images[] = $item['content']['url'];
        }

        return [
            'description' => $description,
            'images' => $images,
            'packages' => $packages,
            'pack' => $pack, //
            'composite' => $composite, //
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
