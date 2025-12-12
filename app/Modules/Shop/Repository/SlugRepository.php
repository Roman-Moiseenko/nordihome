<?php
declare(strict_types=1);

namespace App\Modules\Shop\Repository;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Parser\Entity\CategoryParser;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use JetBrains\PhpStorm\Deprecated;

class SlugRepository
{

    public function getProductParserBySlug($slug):? ProductParser
    {
        if (is_numeric($slug)) return ProductParser::find($slug);
        return ProductParser::where('slug', $slug)->first();
    }

    public function CategoryParserBySlug($slug):? CategoryParser
    {
        if (is_numeric($slug)) return CategoryParser::find($slug);
        return CategoryParser::where('slug', $slug)->first();
    }

    public function getProductBySlug($slug):? Product
    {
        if (is_numeric($slug)) return Product::find($slug);
        return Product::where('slug', '=', $slug)->first();
    }

    public function CategoryBySlug($slug):? Category
    {
        if (is_numeric($slug)) {
            $category = Category::find($slug);
        } else {
            $category = Category::where('slug', '=', $slug)->first();
        }
        return $category;
    }

    public function PageBySlug(string $slug): Page
    {
        return Page::where('slug', $slug)->where('published', true)->first();
    }

    public function getPromotionBySlug($slug): Promotion
    {
        return Promotion::where('slug', $slug)->where('published', true)->firstOrFail();
    }

    public function getGroupBySlug(string $slug): Group
    {
        return Group::where('slug', $slug)->firstOrFail();
    }

    public function PostCategoryBySlug($slug)
    {
        return PostCategory::where('slug', $slug)->first();
    }

    public function PostBySlug($slug)
    {
        return Post::where('slug', $slug)->where('published', true)->first();
    }
}
