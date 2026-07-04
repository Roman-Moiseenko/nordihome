<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\ParserProduct;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use Illuminate\Http\Request;

class CategoryParserService
{
    public function parserProducts(ParserCategory $category): array
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);

        return $parser->getProductsByCategory($category->id);
    }




    public function parserProduct(ParserCategory $category, Request $request): void
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);
        //throw new \DomainException($request->input('product'));
        $parser->parserProductByData($request->input('product'));
    }

    public function addCategory(Request $request): void
    {
        $category = ParserCategory::register(
            $request->string('name')->trim()->value(),
            $request->string('url')->trim()->value(),
            $request->input('parent_id'));
        $category->brand_id = $request->integer('brand_id');
        $category->save();
    }

    public function toggle(ParserCategory $categoryParser): string
    {
        $active = !$categoryParser->active; //Новое состояние
        $categories = ParserCategory::where('_lft', '>=', $categoryParser->_lft)
            ->where('_rgt', '<=', $categoryParser->_rgt)
            ->get();

        $message = $active ? 'Категория(и) добавлена(ы) в парсинг' : 'Категория(и) убрана(ы) из парсинга';

        foreach ($categories as $category) {
            $category->active = $active; //Меняем состояние для всех дочерних категорий и текущей
            $category->save();
            $parser_products = ParserProduct::whereHas('categories', function ($query) use ($category){
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
