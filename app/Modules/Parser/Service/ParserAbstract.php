<?php

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;

abstract class ParserAbstract
{
    const array PARSERS = [
        ParserIkea::class => 'Икеа Польша',
    ];

    protected HttpPage $httpPage;

    protected TranslateService $translate;

    public function __construct(
        TranslateService      $translate,
    )
    {
        $this->httpPage = new HttpPage(); //Без кеша
        $this->translate = $translate;
    }

   // abstract public function findProduct(string $search):? Product;

    abstract public function remainsProduct(string $code): float;

    abstract public function parserCost(ParserProduct $parser): float|bool;

    abstract public function availablePrice(string $code): bool;

    /**
     * Парсит товары по категории текущего бренда и добавляем товары в каталог
     */

    /**
     * Функция поиска данных для товаров по категории парсера
     */

    //abstract protected function parserProductsByUrl(string $domain, string $url);

}

