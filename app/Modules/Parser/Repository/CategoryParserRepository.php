<?php

namespace App\Modules\Parser\Repository;

use App\Modules\Parser\Entity\CategoryParser;

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
            $category->category_name = is_null($category->category) ? null : $category->category->name;
            return $category;
        })->toTree();
        /*
        if (is_null($parent_id)) {
            $categories = Category::defaultOrder()->withDepth()->get()->toTree();
        } else {
            $categories = Category::defaultOrder()->withDepth()->descendantsOf($parent_id)->toTree();
        } */
        // $categories;
    }
}
