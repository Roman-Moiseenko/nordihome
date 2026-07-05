<?php

namespace App\Modules\Parser\Repository;

use App\Modules\Parser\Entity\ParserProduct;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;

class CategoryParserRepository
{

    public function getTree(int $parent_id = null)
    {
        $query = ParserCategory::defaultOrder()->withDepth();
        if (is_null($parent_id)) {
            $categories = $query->get();
        } else {
            $categories = $query->descendantsOf($parent_id);
        }
        return $categories->toTree();
    }

    private function CategoryToArray(ParserCategory $category): array
    {
        return $category->toArray();
    }

    public function CategoryWithToArray(ParserCategory $category): array
    {
        return array_merge($this->CategoryToArray($category), [

            //'category_name' => is_null($category->category) ? null : $category->category->getParentNames(),
            'children' => $this->getTree($category->id),
            'products' => $category->products()->get()->map(function (ParserProduct $product) {
              //  if (is_null($product->product)) dd($product->id);
                return array_merge($product->toArray(), [
                    'name' => $product->product->name,
                    'code' => $product->product->code,
                ]);
            }),
        ]);
    }

    public function forFilters(): array
    {
        return array_map(function (ParserCategory $category) {
            $_depth = str_repeat('-', $category->depth);
            return [
                'id' => $category->id,
                'name' => trim($_depth . ' ' . $category->name),
            ];
        }, $this->withDepth());
    }
    public function withDepth(): array
    {
        return ParserCategory::defaultOrder()->withDepth()->getModels();
    }
}
