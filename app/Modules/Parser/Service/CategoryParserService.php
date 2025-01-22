<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\CategoryParser;
use Illuminate\Http\Request;

class CategoryParserService
{
    public function create(string $name, string $url, int $parent_id = null): CategoryParser
    {
        return CategoryParser::register($name, $url, $parent_id);
    }

    //
    public function parserProducts(CategoryParser $category): void
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);

        $parser->getProductsByCategory($category->id);

    }

    public function setCategory(CategoryParser $category, Request $request): void
    {
        $category->category_id = $request->input('category_id');
        $category->save();
    }


}
