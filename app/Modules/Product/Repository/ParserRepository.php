<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ParserRepository
{
    public function getFilter(Request $request, &$filters): Arrayable
    {
        $query = ProductParser::orderBy('created_at')->whereHas('product', function ($query) {
            $query->where('deleted_at', null);
        });
        //Формируем массив фильтр
        $filters = [];

        if (($product = $request->string('product')->value()) != '' ) {
            $filters['product'] = $product;
            $query->whereHas('product', function ($q) use ($product) {
                $q->whereRaw("LOWER(name) like LOWER('%$product%')")
                    ->orWhere('code', 'like', "%$product%")
                    ->orWhere('code_search', 'like', "%$product%")
                    ->orWhereHas('series', function ($series) use ($product) {
                        $series->whereRaw("LOWER(name) like LOWER('%$product%')");
                    });
            });
        }

        if (($category = $request->integer('category')) > 0) {
            $filters['category'] = $category;
            $query->whereHas('product', function ($qq) use ($category) {
                $qq->whereHas('categories', function ($q) use ($category) {
                    $q->where('id', $category);
                })->orWhere('main_category_id', $category);
            });
        }
        /*
        if ($filters['published'] == 'active') $query->whereHas('product', function ($q) {
            $q->where('published', true);
        });
        */

        if ($request->boolean('published')) {
            $filters['published'] = true;
            $query->whereHas('product', function ($query) {
                $query->where('published', false);
            });
        }
        if ($request->boolean('not_sale')) {
            $filters['not_sale'] = true;
            $query->whereHas('product', function ($query) {
                $query->where('not_sale', true);
            });
        }



        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(ProductParser $parser) => $this->ProductParserToArray($parser));
    }

    private function ProductParserToArray(ProductParser $parser): array
    {
        return array_merge($parser->toArray(), [
            'product' => [
                'id' => $parser->product_id,
                'code' => $parser->product->code,
                'name' => $parser->product->name,
                'category' => $parser->product->category->getParentNames(),
                'image' => $parser->product->getImage('mini'),
            ],
        ]);
    }
}
