<?php

namespace App\Modules\Parser\Service;

use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;

class ParserNB extends ParserAbstract
{

    protected string $brand_name = 'NB';


    public function parserCategories()
    {
        $data = $this->httpPage->getPage($this->brand->url);
        $queries = $this->getQueries($data);
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }

        $children = $menu['children'];
        foreach ($children as $child) {
            $this->addCategory($child);
        }
        return $menu;
    }

    private function addCategory($category, $parent_parser = null, $parent = null): void
    {
        //Есть ли у категории ссылка
        $url = $category['href'] ?? '';
        if (empty($url)) $url = $category['niceUrl'] ?? '';
        if (empty($url)) return;
        //Если категория еще не парсилась
        if (is_null($cat_parser = CategoryParser::where('url', $url)->first())) {
            //Создаем новую категорию парсера
            $name = GoogleTranslateForFree::translate('pl', 'ru', $category['title']);
            $cat_parser = $this->categoryParserService->create($name, $url, $parent_parser);
            //Дублируем категорию в список Категорий магазина
            $cat = Category::register(
                $name,
                $parent,
            );
            $cat_parser->brand_id = $this->brand->id;
            $cat_parser->category_id = $cat->id;
            $cat_parser->save();
        }
        //Дочерние категории
        foreach ($category['children'] as $child) {
            $this->addCategory($child, $cat_parser->id, $cat_parser->category->id);
        }
        //Создать категорию
    }


    private function getQueries(string $data)
    {
        $begin = strpos($data, '<script id="__NEXT_DATA__" type="application/json">');
        $text = substr($data, $begin + strlen('<script id="__NEXT_DATA__" type="application/json">'));
        $end = strpos($text, '</script>');
        $newData = substr($text, 0, $end);
        $array = json_decode($newData, true);
        $queries = $array['props']['pageProps']['dehydratedState']['queries'];
        return $queries;
    }

    public function parserProducts(?int $category_id)
    {

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


    protected function parserProductsByUrl(string $url, bool $is_first_page = true)
    {
        $data = $this->httpPage->getPage($url);
        $queries = $this->getQueries($data);
        $data = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['products']))
                $data = $query['state']['data']['products'];
        }
        $products = $data['items'];
        if (!$is_first_page) return $products; //Уже обрабатывается пагинация
        $pagination = $data['pagination']['lastPage'];
        for ($i = 2; $i <= $pagination; $i++) {
            $products = array_merge(
                $products,
                $this->parserProductsByUrl($url . '?page=' .$i, false)
            );
        }
        dd($products[0]);

    }
}
