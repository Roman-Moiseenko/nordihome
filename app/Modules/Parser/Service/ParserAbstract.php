<?php

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Parser\Entity\ParserProduct;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;

abstract class ParserAbstract
{
    const array PARSERS = [
        ParserNB::class => 'New Balance Польша',
        ParserIkea::class => 'Икеа Польша',
    ];

    protected HttpPage $httpPage;
    protected CategoryParserService $categoryParserService;
    protected Brand $brand;
    protected string $brand_name;
    protected TranslateService $translate;

    public function __construct(
        CategoryParserService $categoryParserService,
        TranslateService      $translate,
    )
    {
        $this->httpPage = new HttpPage(); //Без кеша
        $this->categoryParserService = $categoryParserService;
        $this->brand = Brand::where('name', $this->brand_name)->first();
        $this->translate = $translate;
    }

    abstract public function findProduct(string $search):? Product;

    abstract public function remainsProduct(string $code): float;

    abstract public function parserCost(ParserProduct $parser): float|bool;

    abstract public function availablePrice(string $code): bool;

    /**
     * Парсит товары по категории текущего бренда и добавляем товары в каталог
     */
    final public function getProductsByCategory(?int $category_id): array
    {
        if (is_null($category_id)) {
            $categories = ParserCategory::where('active', true)->where('brand_id', $this->brand->id)->getModels();
        } else {
            $category = ParserCategory::find($category_id);
            $categories = ParserCategory::where('active', true)
                ->where('brand_id', $this->brand->id)
                ->where('_lft', '>=', $category->_lft)
                ->where('_rgt', '<=', $category->_rgt)
                ->getModels();
        }
        /** @var ParserCategory $category */
        $products = [];
        foreach ($categories as $category) {
            if ($category->children()->count() == 0) //Парсим только дочерние
                $products = array_merge($products,
                    $this->parserProductsByCategory($category)
                );

        }
        return $products;
    }
    /**
     * Функция поиска данных для товаров по категории парсера
     */
    abstract protected function parserProductsByCategory(ParserCategory $categoryParser);

    //abstract protected function parserProductsByUrl(string $domain, string $url);

    //Функция распарсивания товара по найденным данным
    abstract public function parserProductByData(array $product): void;
}

