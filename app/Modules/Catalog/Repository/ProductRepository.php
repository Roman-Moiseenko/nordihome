<?php


namespace App\Modules\Catalog\Repository;


use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Equivalent;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

class ProductRepository
{

    public function getIndex($request, &$filters)
    {
        $query = Product::orderBy('name');
        $filters = [];
        if (($category_id = $request->integer('category')) > 0) {
            $filters['category'] = $category_id;
            //Получить все дочерние категории
            $categories = Category::find($category_id)->getChildrenIdAll();

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
            'category_name' => $product->category->getParentNames(),
            'price' => $product->getPriceRetail(),
            'bulk' => $product->getPriceBulk(),
            'quantity' => $product->getQuantity(),
            'reserve' => $product->getReserveCount(),
            'trashed' => $product->trashed(),
        ]);
    }


    public function search(string $search, int $take = 10, array $include_ids = [], bool $isInclude = true): array
    {
        $query = Product::orderBy('name')->where('deleted_at', null)->where(function ($query) use ($search) {
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
