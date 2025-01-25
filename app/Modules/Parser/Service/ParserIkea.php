<?php
declare(strict_types=1);

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Product\Entity\Product;

class ParserIkea extends ParserAbstract
{

    protected string $brand_name = 'Икеа';

    const API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/meta-data/navigation/catalog-products-slim.json?cb=2dy1g6t4pz';


    public function parserCategories()
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

    protected function parserProductsByUrl(string $url)
    {
        // TODO: Implement parserProductsByUrl() method.
    }
}
