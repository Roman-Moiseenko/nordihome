<?php

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;

abstract class ParserAbstract
{
    const PARSERS = [
        ParserNB::class => 'New Balance Польша',
    ];

    protected HttpPage $httpPage;
    protected CategoryParserService $categoryParserService;
    protected Brand $brand;
    protected string $brand_name;

    public function __construct(
        HttpPage $httpPage,
        CategoryParserService  $categoryParserService,
    )
    {
        $this->httpPage = $httpPage;
        $this->categoryParserService = $categoryParserService;
        $this->brand = Brand::where('name', $this->brand_name)->first();
    }
    abstract public function findProduct(string $search): Product;

    abstract public function remainsProduct(string $code): float;

    abstract public function costProduct(string $code): float;

    abstract public function availablePrice(string $code): bool;

    final public function getProductsByCategory(?int $category_id)
    {
        if (is_null($category_id)) {
            $categories = CategoryParser::where('active', true)->getModels();
        } else {
            $category = CategoryParser::find($category_id);
            $categories = CategoryParser::where('active', true)
                ->where('_lft', '>=', $category->_lft)
                ->where('_rgt', '<=', $category->_rgt)
                ->getModels();
        }
        /** @var CategoryParser $category */
        foreach ($categories as $category) {
            if ($category->children()->count() == 0) //Парсим только дочерние
                $this->parserProductsByUrl($this->brand->url . '/' . $category->url);
        }
    }
    abstract protected function parserProductsByUrl(string $url);

}
