<?php

namespace App\Modules\Parser\Infrastructure\Services;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Parser\Application\Interfaces\IkeaProductApiInterface;

class IkeaProductApi implements IkeaProductApiInterface
{
    const string API_URL_PRODUCTS = 'https://sik.search.blue.cdtapps.com/pl/pl/product-list-page/more-products?category=%s&start=%s&end=%s';
    const string API_URL_PRODUCT = 'https://sik.search.blue.cdtapps.com/pl/pl/search-result-page?q=%s';
    const string API_URL_QUANTITY = 'https://api.ingka.ikea.com/cia/availabilities/ru/pl?itemNos=%s&expand=StoresList,Restocks,SalesLocations,DisplayLocations,ChildItems,FoodAvailabilities';

    public function __construct(
        private readonly HttpPage $httpPage,
    ) {}

    /**
     * Получить список товаров по категории
     * @param string $ikeaId
     * @return array
     */
    public function getProductsByCategory(string $ikeaId): array
    {
        $products = [];
        $start = 0;
        $end = 1000;
        do {
            $_url = sprintf(self::API_URL_PRODUCTS, $ikeaId, $start, $end);
            $json_product = $this->httpPage->getPage($_url);
            if (!is_null($json_product)) {
                $_array = json_decode($json_product, true);
                $list = $_array['moreProducts']['productWindow'] ?? [];
            } else {
                $list = [];
            }
            $products = array_merge($products, $list);
            $start += 1000;
            $end += 1000;
        } while (count($list) == 1000);

        return  $products;
    }

    /**
     * Получить данные товара по коду
     * @return array|null массив product из ответа API, или null если не найден
     */
    public function getProductByCode(string $code): ?array
    {
        $url = sprintf(self::API_URL_PRODUCT, $code);
        $jsonData = $this->httpPage->getPage($url);
        if (is_null($jsonData)) return null;

        $jsonData = json_decode($jsonData, true);
        if (empty($jsonData['searchResultPage']['products']['main']['items'])) return null;

        return $jsonData['searchResultPage']['products']['main']['items'][0]['product'];
    }

    /**
     * Получить и распарсить страницу товара
     * @return array|null массив pageProps.product из HTML страницы
     */
    public function getProductPage(string $pipUrl): ?array
    {
        $pageProduct = $this->httpPage->getPage($pipUrl);
        if (is_null($pageProduct)) return null;

        $pattern = '#<script type="text\/hydrate">(.+?)<\/script>#su';
        preg_match_all($pattern, $pageProduct, $res);

        foreach ($res[1] as $item_res) {
            $_data = json_decode($item_res, true);
            if (isset($_data["pageProps"])) {
                return $_data["pageProps"]["product"];
            }
        }

        return null;
    }

    /**
     * Получить остатки товара по коду
     * @return array|null массив availabilities из ответа API
     */
    public function getAvailability(string $code): ?array
    {
        $url = sprintf(self::API_URL_QUANTITY, $code);
        $json_product = $this->httpPage->getPage($url, '_cache');
        if (is_null($json_product)) return null;

        $_array = json_decode($json_product, true);
        if ($_array === null) return null;

        return $_array['availabilities'] ?? null;
    }
}
