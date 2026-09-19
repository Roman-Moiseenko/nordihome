<?php

namespace App\Modules\Shop\Application\Queries\Group;

use App\Modules\Storefront\Application\DTOs\ClientContext;

class GroupPageQuery
{
    public function __construct()
    {
    }
    public function execute(string $slug, array $params, ClientContext $getClient)
    {
        //MAINDO Переделать на Query под общий список либо свой формат страницы
        return null;
    }
}
