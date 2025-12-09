<?php

namespace App\Modules\Parser\Repository;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;

class ProductParserRepository
{

    public function getIndex(\Illuminate\Http\Request $request, &$filters)
    {
        $query = ProductParser::orderBy('maker_id');
        $filters = [];
        if (($category_id = $request->integer('category')) > 0) {
            $filters['category'] = $category_id;
            //Получить все дочерние категории
            $categories = CategoryParser::find($category_id)->getChildrenIdAll();

            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('id', $categories);
            })->orWhereIn('main_category_id', $categories);
        }

        if (!empty($name = $request->string('name')->trim()->value())) {
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')")
                ->orWhere('code', 'like', "%$name%")
                ->orWhere('code_search', 'like', "%$name%");
        }
        if (!is_null($show = $request->input('show'))) {
            $filters['show'] = $show;
            if ($show == 'availability') $query->where('availability', true);
            if ($show == 'not_availability') $query->where('availability', false);
            if ($show == 'fragile') $query->where('fragile', true);
            if ($show == 'sanctioned') $query->where('sanctioned', true);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(ProductParser $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(ProductParser $parser): array
    {
        return array_merge($parser->toArray(), [
            'product_id' => $parser->product_id,
            'image' => $parser->product->miniImage(),
            'code' => $parser->product->code,
            'category_name' => $parser->product->category->getParentNames(),
            'name' => $parser->product->name,

            //'price' => $parser->getPriceRetail(),



        ]);
    }
}
