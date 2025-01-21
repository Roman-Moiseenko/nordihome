<?php

namespace App\Modules\NBRussia\Service;

use App\Modules\Base\Service\GoogleTranslateForFree;
use App\Modules\Base\Service\HttpPage;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Service\CategoryParserService;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;

class ParserService
{
    private HttpPage $httpPage;
    private CategoryParserService $categoryParserService;
    private Brand $brand;

    public function __construct(
        HttpPage $httpPage,
        CategoryParserService  $categoryParserService,
    )
    {
        $this->httpPage = $httpPage;
        $this->categoryParserService = $categoryParserService;
        $this->brand = Brand::where('name', 'NB')->first();
    }

    public function parserCategories()
    {

        $data = $this->httpPage->getPage($this->brand->url);
        $queries = $this->getQueries($data);
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }
        //TODO Добавление категорий

        $children  = $menu['children'];
        foreach ($children as $child) {
            $this->addCategory($child);
        }
        return $menu;
    }

    private function addCategory($category, $parent_parser = null, $parent = null): void
    {
        //Если категория еще не парсилась
        if (is_null($cat_parser = CategoryParser::where('url', $category['niceUrl'])->first())) {
            //Создаем новую категорию парсера
            $name = GoogleTranslateForFree::translate('pl','ru', $category['title']);
            $cat_parser = $this->categoryParserService->create($name, $category['niceUrl'], $parent_parser);
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
            $this->addCategory($child, $cat_parser->id, $cat->id);
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

    public function parserProducts()
    {
        //TODO
        // ссылка $parser_category->brand->url . '/' . $parser_category->url
    }

}
