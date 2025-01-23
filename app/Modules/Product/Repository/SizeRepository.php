<?php

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\CategorySize;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class SizeRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = CategorySize::orderBy('name');
        $filters = [];

        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(CategorySize $categorySize) => $this->CategoryToArray($categorySize));
    }

    private function CategoryToArray(CategorySize $categorySize): array
    {
        return array_merge($categorySize->toArray(), [
            'count' => $categorySize->sizes()->count(),
            'sizes' => $categorySize->sizes,
        ]);
    }

    public function CategorySizeWithToArray(CategorySize $categorySize): array
    {
        return array_merge($this->CategoryToArray($categorySize), [

        ]);
    }
}
