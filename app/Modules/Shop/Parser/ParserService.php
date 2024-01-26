<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Entity\Dimensions;
use App\Entity\Photo;
use App\Events\ProductHasParsed;
use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;


class ParserService
{
    const STORE = 203; //Код магазина в стране
    const API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';
    const API_URL_QUANTITY = 'https://api.ingka.ikea.com/cia/availabilities/ru/pl?itemNos=%s&expand=StoresList,Restocks';

    CONST STORES = [
        203 => 'Гданьск',
        188 => 'Рашин',
        204 => 'Краков',
        205 => 'Познань',
        294 => 'Бабжице',
        306 => 'Катовице',
        307 => 'Варшава',
        311 => 'Люблин',
        329 => 'Лодзь',
        429 => 'Быдгощ',
        583 => 'Щецин',
    ];
    public string $telegram_token = '5672886799:AAEx8Wxsms8nroZG8H2Muvvhy65h0snbNGA'; //Телеграм токен
    public string $telegram_chat_id = '-743591708'; //ID чат-бота
    private HttpPage $httpPage;
    private Options $options;

    public function __construct(HttpPage $httpPage)
    {
        $this->httpPage = $httpPage;
        $this->options = new Options();
    }

    public function findProduct(Request $request): Product
    {
        $code = $this->formatCode($request['search']);
        $product = Product::where('code_search', $code)->first();//Ищем товар в базе
        if (empty($product)) {
            //Парсим основные данные
            $parser_product = $this->parsingData($code);
            //1. Добавляем черновик товара (Артикул, Главное фото, Название, Краткое описание, Базовая цена, published = false)
            $product = Product::register(
                $parser_product['name'],
                $this->toCode($code),
                (Category::where('name', 'Прочее')->first())->id,
            );
            $product->short = $parser_product['description'];
            $product->brand_id = (Brand::where('name', 'Икеа')->first())->id;
            $product->dimensions = Dimensions::create(0, 0, 0, $parser_product['weight'], Dimensions::MEASURE_KG);
            //Опции магазина
            $product->pre_order = $this->options->shop->pre_order;
            $product->only_offline =  $this->options->shop->only_offline;
            $product->not_delivery = !$this->options->shop->delivery_all;
            $product->not_local = !$this->options->shop->delivery_local;


            $product->save();
            $product->photo()->save(Photo::uploadByUrl($parser_product['image']));
            $product->refresh();

            //4. Создаем ProductParsing
            $productParser = $this->createProductParsing($product->id, $parser_product);
            $quantity = $this->parsingQuantity($code);
            $productParser->setQuantity($quantity);

            event(new ProductHasParsed($product));
            return $product;
        }

        $productParser = ProductParser::where('product_id', $product->id)->first();
        if (empty($productParser)) {
            $parser_product = $this->parsingData($product->code_search);
            $productParser = $this->createProductParsing($product->id, $parser_product);
        }

        if ($productParser->updated_at->lt(now()->addHours(3))) {
            $quantity = $this->parsingQuantity($code);
            $productParser->setQuantity($quantity);
        }
        return $product;
    }

    //TODO Функция для Cron - раз в сутки, парсим все цены товаров из ProductParsing
    public function parserCost(string $code): float
    {
        $result = $this->parsingData($code, true);
        return $result['price'];
    }

    public function parserImage(string $linkProduct): array
    {
        $result = [];
        $pageProduct = $this->httpPage->getPage($linkProduct, '_cache');
        preg_match_all('#data-hydration-props="(.+?)"#su', $pageProduct, $res);
        $_res = $res[1][0];
        $_res = str_replace('&quot;', '"', $_res);
        $_data = json_decode($_res, true);
        foreach ($_data['mediaGrid']['fullMediaList'] as $item) {
            if ($item['type'] == 'image' && $item['content']['type'] != 'MAIN_PRODUCT_IMAGE')
                $result[] = $item['content']['url'];
        }
        return $result;
    }

    public function parsingQuantity(string $code): array
    {
        $url = sprintf(self::API_URL_QUANTITY, $code);
        $json_quantity = $this->httpPage->getPage($url, '_cache');
        $array_quantity = json_decode($json_quantity, true);
        $result = [];
        if ($array_quantity == null)
            throw new \DomainException('Неверный артикул или ссылка');
        foreach ($array_quantity['data'] as $item) {
            if (isset($item['availableStocks'])) {
                $_store = (int)$item['classUnitKey']['classUnitCode']; //Номер склада
                $_quantity = (int)$item['availableStocks'][0]['quantity']; //Кол-во на складе
                if ($_store != 0) $result[$_store] = $_quantity;
            }
        }
        return $result;
    }

    private function formatCode(string $code): string
    {
        $res = [];
        if (str_contains($code, 'ikea')) {//Если ссылка вытаскиваем 8цифр подряд
            preg_match_all('/\d{8}/', $code, $res);
        } else {
            preg_match_all('/\d/', $code, $res);
        }
        $result = implode($res[0]);
        if (strlen($result) == 0)
            throw new \DomainException('Неверный артикул товара или ссылка');
        if (strlen($result) != 8)
            throw new \DomainException('Неверный артикул товара, количество цифр должно быть равно 8 или неверная ссылка');
        return $result;
    }

    private function createProductParsing(int $product_id, array $parser_product): ProductParser
    {
        return ProductParser::register(
            $product_id,
            $parser_product['pack'],
            $parser_product['price'],
            $parser_product['composite'],
            $parser_product['link']
        );
    }

    private function toCode(string $code): string
    {
        if (empty($code)) return '';
        $code = substr_replace($code, '.', 6, 0);
        return substr_replace($code, '.', 3, 0);
    }

    private function parsingData(string $code, bool $onlyPrice = false): array|float
    {
        $url = sprintf(self::API_URL_PRODUCT, $code); //API для поиска товара
        $json_product = $this->httpPage->getPage($url, '_cache');

        $_array = json_decode($json_product, true);

        if ($_array == null)
            throw new \DomainException('На сайте содержаться неверные данные о продукте');
        $item = $_array['searchResultPage']['products']['main']['items'][0]['product'];
        //Парсим первычный JSON
        //$code = $this->toCode($item['itemNo']); //Добавляем точки к артикулу itemNoGlobal , Id , itemNo
        $name = $item['name'];
        $link = $item['pipUrl'];
        $image = $item['mainImageUrl'];
        $price = $item['salesPrice']['numeral'];

        //Проверяем, была ли предыдущая цена.
        if (isset($item['salesPrice']['previous'])) {
            $_previous = (float)(str_replace(' ', '', $item['salesPrice']['previous']['wholeNumber']) . '.' . $item['salesPrice']['previous']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }
        //TODO Для крона только цена, возможно разбить на 2 функции ...
        if ($onlyPrice) return $price;

        //Сканируем страницу для остальных параметров
        $pageProduct = $this->httpPage->getPage($link, '_cache');
        preg_match_all('#data-hydration-props="(.+?)"#su', $pageProduct, $res);
        $_res = $res[1][0];
        $_res = str_replace('&quot;', '"', $_res);
        $_data = json_decode($_res, true);

        //throw new \DomainException($_res);
        ////Определяем есть ли составные артикулы

        $_sub = $_data['stockcheckSection']['subProducts']; //availabilityHeaderSection

        $composite = [];
        if (count($_sub) != 0) {
            foreach ($_sub as $_item) {
                $composite[] = $this->toCode($_item['itemNo']) . ' - ' . $_item['quantity'] . 'шт.';
            }
        }
        ////Кол-во пачек
        $pack = $_data['stockcheckSection']['numberOfPackages'];
        ////Вычисляем вес
        $weight = 0;
        $_packages = $_data['stockcheckSection']['packagingProps']['packages'];
        foreach ($_packages as $_item) {
            if (count($_item['measurements']) != 0) //Пропускаем для самого товара, только по составным
                foreach ($_item['measurements'] as $measurement) //Если товар в 1 пачке разбит на несколько
                    $weight += ($this->toWeight($measurement)  * $_item['quantity']['value']);//Умножаем на кол-во пачек данного товара
        }

        ////Описание и перевод
        $description = $_data['pipPriceModule']['productDescription'] .
            (empty($_data['pipPriceModule']['measurementText']) ? '' : ', ' . $_data['pipPriceModule']['measurementText']);

        $tr = new GoogleTranslateForFree();
        $description = $tr->translate('pl', 'ru', $description, 5);

        return [
            'name' => $name,
            'description' => $description,
            'link' => $link,
            'image' => $image,
            'weight' => $weight,
            'price' => $price,
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
}
