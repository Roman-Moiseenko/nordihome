<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use Illuminate\Http\Request;

class CategoryParserService
{
    public function parserProduct(ParserCategory $category, Request $request): void
    {
        $parser_class = $category->brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);
        //throw new \DomainException($request->input('product'));
        $parser->parserProductByData($request->input('product'));
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
