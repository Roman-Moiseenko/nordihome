<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Http\Request;

class ParserRepository
{
    public function getFilter(Request $request, &$filters)
    {
        //Формируем массив фильтр
        $filters = [
            'product' => $request['product'] ?? null,
            'category' => $request['category_id'] ?? null,
            'published' => $request['published'] ?? null,
            'not_sale' => $request['not_sale'] ?? null,
        ];
        $_filter_count = 0;
        $_filter_text = '';
        foreach ($filters as $key => $item) {
            if (!is_null($item)) {
                $_filter_count++;
                if ($key == 'product') $_filter_text .= $item . ', ';
                if ($key == 'category') $_filter_text .= Category::find($item)->name . ', ';
                if ($key == 'published') $_filter_text .= $item;
                if ($key == 'not_sale') $_filter_text .= $item;
            }
        }
        $filters['count'] = $_filter_count;
        $filters['text'] = $_filter_text;

        //Формируем запрос по фильтру

        $query = ProductParser::orderBy('created_at');
        $product = $filters['product'];

        if (!empty($filters['product'])) $query->whereHas('product', function ($q) use ($product) {
            $q->where('name', 'like', "%$product%")
                ->orWhere('code', 'like', "%$product%")
                ->orWhere('code_search', 'like', "%$product%")
                ->orWhereHas('series', function ($series) use ($product){
                    $series->where('name', 'like', "%$product%");
                });
        });

        if (!empty($filters['category'])) {
            $query->whereHas('product', function ($qq) use ($filters) {
                $qq->whereHas('categories', function ($q) use ($filters) {
                    $q->where('id', $filters['category']);
                })->orWhere('main_category_id', $filters['category']);
            });
        }
        if ($filters['published'] == 'active') $query->whereHas('product', function ($q) {$q->where('published', true);});
        if ($filters['published'] == 'draft') $query->whereHas('product', function ($q) {$q->where('published', false);});
        if ($filters['not_sale'] != null) $query->whereHas('product', function ($q) {$q->where('not_sale', true);});
        return $query;
    }
}
