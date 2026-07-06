<?php
declare(strict_types=1);

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;

class ParserIkea extends ParserAbstract
{

    protected string $brand_name = 'Икеа';
    const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/navigation/catalog-products-slim.json?cb=85p6e40iet';
    //const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/meta-data/navigation/catalog-products-slim.json?cb=2dy1g6t4pz';
    const string API_URL_PRODUCTS = 'https://sik.search.blue.cdtapps.com/pl/pl/product-list-page/more-products?category=%s&start=%s&end=%s';
    const string API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';

    const string API_URL_QUANTITY = 'https://api.ingka.ikea.com/cia/availabilities/ru/pl?itemNos=%s&expand=StoresList,Restocks,SalesLocations,DisplayLocations,ChildItems,FoodAvailabilities';
    private ParserLogService $logService;

    public function __construct(
                                TranslateService      $translate, ParserLogService $logService)
    {
        parent::__construct($translate);

        $this->logService = $logService;
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

    public function parserCost(ParserProduct $parser): float|bool
    {
        $code = $parser->maker_id; //product->code;

        $url = sprintf(self::API_URL_PRODUCT, $code); //API для поиска товара
        $json_product = $this->httpPage->getPage($url, '_cache');
        $_array = json_decode($json_product, true);

        //Товар теперь недоступен
        if ($_array == null || empty($_array['searchResultPage']['products']['main']['items'])) {
            $parser->availability = false;
            $parser->save();
           // $this->logService->addLog($parser->id, ParserLogItem::STATUS_DEL);
        }

        $item = $_array['searchResultPage']['products']['main']['items'][0]['product']['salesPrice'];
        $price = $item['numeral'];
        if (isset($item['previous'])) {
            $_previous = (float)(str_replace(' ', '', $item['previous']['wholeNumber']) . '.' . $item['previous']['decimals']);
            if ($_previous > (float)$price) $price = $_previous;
        }
        //Изменилась цена
        if ($parser->price_sell != $price) {

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


}
