<?php

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;

abstract class ParserAbstract
{
    const PARSERS = [
        ParserNB::class => 'New Balance Польша',
        ParserIkea::class => 'Икеа Польша',
    ];

    protected HttpPage $httpPage;
    protected CategoryParserService $categoryParserService;
    protected Brand $brand;
    protected string $brand_name;
    protected TranslateService $translate;

    public function __construct(
        HttpPage              $httpPage,
        CategoryParserService $categoryParserService,
        TranslateService      $translate,
    )
    {
        $this->httpPage = $httpPage;
        $this->categoryParserService = $categoryParserService;
        $this->brand = Brand::where('name', $this->brand_name)->first();
        $this->translate = $translate;
    }

    abstract public function findProduct(string $search): Product;

    abstract public function remainsProduct(string $code): float;

    abstract public function costProduct(string $code): float;

    abstract public function availablePrice(string $code): bool;

    /**
     * Парсит товары по категории текущего бренда и добавляем товары в каталог
     */
    final public function getProductsByCategory(?int $category_id): array
    {
        if (is_null($category_id)) {
            $categories = CategoryParser::where('active', true)->where('brand_id', $this->brand->id)->getModels();
        } else {
            $category = CategoryParser::find($category_id);
            $categories = CategoryParser::where('active', true)
                ->where('brand_id', $this->brand->id)
                ->where('_lft', '>=', $category->_lft)
                ->where('_rgt', '<=', $category->_rgt)
                ->getModels();
        }
        /** @var CategoryParser $category */
        $products = [];
        foreach ($categories as $category) {
            if ($category->children()->count() == 0) //Парсим только дочерние
                $products = array_merge($products,
                    $this->parserProductsByUrl($this->brand->url . '/' . $category->url)
                );

        }
        return $products;
    }

    /**
     * Функция поиска данных для товаров по урлу
     */
    abstract protected function parserProductsByUrl(string $url);

    //Функция распарсивания товара по найденным данным
    abstract public function parserProductByData(array $product): void;
}

