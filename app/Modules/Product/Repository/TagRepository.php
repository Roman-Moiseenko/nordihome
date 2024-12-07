<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use Illuminate\Http\Request;

class TagRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = Tag::orderBy('name');
        $filters = [];

        if ($request->has('product')) {
            $product = $request->string('product');
            $filters['product'] = $product;
            $query->whereHas('products', function ($query) use ($product) {
                $query->where(function ($q) use ($product) {
                    $q->whereRaw("LOWER(name) like LOWER('%$product%')")
                        ->orWhere('code', 'like', "%$product%")
                        ->orWhere('code_search', 'like', "%$product%");
                });
            });

        }
        if ($request->has('tag')) {
            $name = $request->string('tag')->value();
            $filters['tag'] = $name;
            $query->whereRaw("LOWER(name) like LOWER('%$name%')");
        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Tag $tag) => $this->TagToArray($tag));
    }


    public function exists(mixed $id): bool
    {
        return !is_null(Tag::find($id));
    }

    private function TagToArray(Tag $tag): array
    {
        return array_merge($tag->toArray(), [
            'quantity' => $tag->products()->count(),
        ]);
    }

    public function TagWithToArray(Tag $tag): array
    {
        return array_merge($this->TagToArray($tag), [
            'products' => $tag->products()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category->getParentNames(),
                ];
            }),
        ]);
    }
}
