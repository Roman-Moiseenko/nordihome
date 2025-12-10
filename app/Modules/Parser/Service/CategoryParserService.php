<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use Illuminate\Http\Request;

class CategoryParserService
{
    #[\Deprecated]
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
        $categories = CategoryParser::where('_lft', '>=', $category->_lft)->where('_rgt', '<=', $category->_rgt)->get();
        foreach ($categories as $category) {
            if (is_null($category->category_id)) {
                $category->category_id = $request->input('category_id');
                $category->save();
            }
        }
        //$category->category_id = $request->input('category_id');
        //$category->save();
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

    public function toggle(CategoryParser $categoryParser): string
    {
        $active = !$categoryParser->active; //Новое состояние
        $categories = CategoryParser::where('_lft', '>=', $categoryParser->_lft)
            ->where('_rgt', '<=', $categoryParser->_rgt)
            ->get();

        $message = $active ? 'Категория(и) добавлена(ы) в парсинг' : 'Категория(и) убрана(ы) из парсинга';

        foreach ($categories as $category) {
            $category->active = $active; //Меняем состояние для всех дочерних категорий и текущей
            $category->save();
            $parser_products = ProductParser::whereHas('categories', function ($query) use ($category){
                $query->where('id', $category->id);
            })->get();

            foreach ($parser_products as $parser_product) {
                $parser_product->availability = $active; //Меняем состояние для всех товаров из дочерних категорий
                $parser_product->save();
            }
        }
        return $message;
    }




}
