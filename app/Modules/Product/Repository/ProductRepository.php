<?php


namespace App\Modules\Product\Repository;


use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    /**
     * Репозиторий для передачи данных в другие модули (шлюз)
     * Либо сделать Шлюз get + query
     * \Bus\FletchProduct и \Bus\QueryProduct
     * в QueryProduct setCountForSell(%id, %count) и если нет ТУ то setPriceForSell($id, $price)
     */


    public function getIndex($request, &$filters)
    {
        $query = Product::orderBy('name');
        $filters = [];
        if (($category_id = $request->integer('category')) > 0) {
            $filters['category'] = $category_id;
            $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where('id', $category_id);
            })->orWhere('main_category_id', $category_id);
        }

        if (!empty($name = $request->string('name')->trim()->value())) {
            $filters['name'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')")
                ->orWhere('code', 'like', "%$name%")
                ->orWhere('code_search', 'like', "%$name%");
        }
        if (!is_null($show = $request->input('show'))) {
            $filters['show'] = $show;
            if ($show == 'active') $query->where('published', true);
            if ($show == 'draft') $query->where('published', false);
            if ($show == 'delete') $query->onlyTrashed();
            if ($show == 'not_sale') $query->where('not_sale', true);
        }



        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Product $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(Product $product): array
    {
        return array_merge($product->toArray(), [
            'image' => $product->getImage('mini'),
            'category_name' => $product->category->getParentNames(),
            'price' => $product->getPriceRetail(),
            'quantity' => $product->getQuantity(),
            'reserve' => $product->getReserveCount(),
            'trashed' => $product->trashed(),
        ]);
    }

    public function ProductWithToArray(Product $product): array
    {
        return array_merge($this->ProductToArray($product), [

        ]);
    }


    public function search(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true): array
    {
        $query = Product::orderBy('name')->where(function ($query) use ($search) {
            $query->where('code_search', 'LIKE', "%$search%")->orWhere('code', 'LIKE', "%$search%")
                ->orWhereRaw("LOWER(name) like LOWER('%$search%')");
        });

        if (!empty($include_ids)) {
            if ($isInclude) {
                $query = $query->whereIn('id', $include_ids);
            } else {
                $query = $query->whereNotIn('id', $include_ids);
            }
        }
        return $query->take($take)->getModels();
    }


    public function getFilter(Request $request, &$filters)
    {

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


        $query = Product::orderBy('name');
        $product = $filters['product'];

        if (!empty($filters['product'])) $query->where(function ($q) use ($product) {
            $q->where('name', 'like', "%$product%")
                ->orWhere('code', 'like', "%$product%")
                ->orWhere('code_search', 'like', "%$product%")
                ->orWhereHas('series', function ($series) use ($product) {
                    $series->where('name', 'like', "%$product%");
                });
        });

        if (!empty($filters['category'])) {
            $query->where(function ($qq) use ($filters) {
                $qq->whereHas('categories', function ($q) use ($filters) {
                    $q->where('id', $filters['category']);
                })->orWhere('main_category_id', $filters['category']);
            });
        }
        if ($filters['published'] == 'active') $query->where('published', true);
        if ($filters['published'] == 'draft') $query->where('published', false);
        if ($filters['published'] == 'delete') $query->onlyTrashed();
        if ($filters['not_sale'] != null) $query->where('not_sale', true);
        return $query;
    }


}
