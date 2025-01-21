<?php

namespace App\Modules\NBRussia\Service;

use App\Modules\Base\Service\HttpPage;

class ParserService
{
    private HttpPage $httpPage;

    public function __construct(HttpPage $httpPage)
    {
        $this->httpPage = $httpPage;
    }

    public function parserCategories()
    {
        $data = $this->httpPage->getPage('https://nbsklep.pl/');
        $queries = $this->getQueries($data);
        $menu = [];
        foreach ($queries as $query) {
            if (isset($query['state']['data']['menu']))
                $menu = $query['state']['data']['menu'];
        }
        //TODO Добавление категорий
        return $menu;
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
    }

}
