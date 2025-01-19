<?php

namespace App\Modules\NBRussia\Service;

use App\Modules\Shop\Parser\HttpPage;

class TestService
{

    private HttpPage $httpPage;

    public function __construct(HttpPage $httpPage)
    {
        $this->httpPage = $httpPage;
    }

    public function parser()
    {
        $url = 'https://nbsklep.pl/api/graphql/frontend/menu/listing';
        $data = $this->httpPage->getPage($url);
        dd($data);
    }
}
