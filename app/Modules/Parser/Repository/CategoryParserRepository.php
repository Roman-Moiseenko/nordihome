<?php

namespace App\Modules\Parser\Repository;

use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Product;

class CategoryParserRepository
{

    public function getTree(int $parent_id = null)
    {
        $query = CategoryParser::defaultOrder()->withDepth();
        if (is_null($parent_id)) {
            $categories = $query->get();
        } else {
            $categories = $query->descendantsOf($parent_id);
        }
        return $categories->map(function (CategoryParser $category) {
            $category->brand_name = $category->brand->name;
            $category->category_name = is_null($category->category) ? null : $category->category->getParentNames();
            return $category;
        })->toTree();

    }

    private function CategoryToArray(CategoryParser $category): array
    {
        return $category->toArray();
    }

    public function CategoryWithToArray(CategoryParser $category): array
    {
        return array_merge($this->CategoryToArray($category), [
            'brand_name' => $category->brand->name,
            'category_name' => is_null($category->category) ? null : $category->category->getParentNames(),
            'children' => $this->getTree($category->id),
            'products' => $category->products()->get()->map(function (ProductParser $product) {
                return array_merge($product->toArray(), [
                    'name' => $product->product->name,
                    'code' => $product->product->code,
                ]);
            }),
        ]);
    }
}
