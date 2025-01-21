<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\CategoryParser;

class CategoryParserService
{
    public function create(string $name, string $url, int $parent_id = null): CategoryParser
    {
        return CategoryParser::register($name, $url, $parent_id);
    }
}
