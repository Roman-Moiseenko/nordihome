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

}
