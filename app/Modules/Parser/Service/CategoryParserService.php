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

    public function parserProducts(CategoryParser $category): array
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);

        return $parser->getProductsByCategory($category->id);
    }

    public function setCategory(CategoryParser $category, Request $request): void
    {
        $category->category_id = $request->input('category_id');
        $category->save();
    }

    public function parserProduct(CategoryParser $category, Request $request): void
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);
        //throw new \DomainException($request->input('product'));
        $parser->parserProductByData($request->input('product'));
    }

    public function addCategory(Request $request): void
    {
        $category = CategoryParser::register(
            $request->string('name')->trim()->value(),
            $request->string('url')->trim()->value(),
            $request->input('parent_id'));
        $category->brand_id = $request->integer('brand_id');
        $category->save();
    }


}
