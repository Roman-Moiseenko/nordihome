<?php

namespace App\Modules\Unload\Repository;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use App\Modules\Unload\Entity\Feed;
use Illuminate\Http\Request;

class FeedRepository
{

    public function getIndex(Request $request, &$filters): array
    {
        $query = Feed::orderBy('id');
        $filters = [];

        return $query->get()
            ->map(fn(Feed $order) => $this->FeedToArray($order))->toArray();
    }

    public function FeedToArray(Feed $feed): array
    {
        return array_merge($feed->toArray(), [
            'products_in' => Product::whereIn('id', $feed->products_in)
                ->get()
                ->map(fn(Product $product) => ['id' => $product->id, 'code' => $product->code])
                ->toArray(),
            'products_out' => Product::whereIn('id', $feed->products_out)
                ->get()
                ->map(fn(Product $product) => ['id' => $product->id, 'code' => $product->code])
                ->toArray(),
            'tags_in' => Tag::whereIn('id', $feed->tags_in)
                ->get()
                ->map(fn(Tag $tag) => ['id' => $tag->id, 'name' => $tag->name])
                ->toArray(),
            'tags_out' => Tag::whereIn('id', $feed->tags_out)
                ->get()
                ->map(fn(Tag $tag) => ['id' => $tag->id, 'name' => $tag->name])
                ->toArray(),
        ]);
    }

    public function GetProducts(Feed $feed): array
    {
        $tag_In_product_ids = Product::whereHas('tags', function ($query) use ($feed) {
            $query->whereIn('id', $feed->tags_in);
        })->get()->pluck('id')->toArray();

        $tag_Out_product_ids = Product::whereHas('tags', function ($query) use ($feed) {
            $query->whereIn('id', $feed->tags_out);
        })->get()->pluck('id')->toArray();

        $category_In_product_ids = Product::whereHas('categories', function ($query) use ($feed) {
            $query->whereIn('id', $feed->categories_in);
        })->orWhereIn('main_category_id', $feed->categories_in)
            ->get()->pluck('id')->toArray();
        $category_Out_product_ids = Product::whereHas('categories', function ($query) use ($feed) {
            $query->whereIn('id', $feed->categories_out);
        })->orWhereIn('main_category_id', $feed->categories_out)
            ->get()->pluck('id')->toArray();

        $ins = array_unique(array_merge($feed->products_in, $tag_In_product_ids, $category_In_product_ids));
        $outs = array_unique(array_merge($feed->products_out, $tag_Out_product_ids, $category_Out_product_ids));

        $ids = array_diff($ins, $outs);

        return Product::whereIn('id', $ids)
            ->get()
            ->map(fn(Product $product) => $this->ProductToArray($product))
            ->toArray();
    }

    public function GetCategories(array $products): array
    {
        $ids = [];
        $cats = [];
        foreach ($products as $product) $ids[] = $product['category'];
        $ids = array_unique($ids);
        $categories = Category::whereIn('id', $ids)->get();
        foreach ($categories as $category) {
            $this->cats($cats, $category);
        }

        return array_unique($cats, SORT_REGULAR);
    }

    private function cats(array &$cats, Category $category): void
    {
        $cat['id'] = $category->id;
        $cat['name'] = $category->name;
        if ($category->parent_id != null) {
            $cat['parent'] = $category->parent_id;
            $this->cats($cats, $category->parent);
        }
        $cats[] = $cat;
    }

    private function ProductToArray(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'images' => $product->gallery->map(fn(Photo $photo) => $photo->getUploadUrl())->toArray(),
            'url' => route('shop.product.view', $product->slug),
            'price' => $product->getPrice(),
            'preprice' => $product->getPrice(true),
            'category' => $product->main_category_id,
            'store' => true,
            'pickup' => true,
            'delivery' => true,
        ];
    }

    public function getInfo(Feed $feed): array
    {
        $trader = Trader::default();

        return [
            'name' => empty($feed->set_title) ? $trader->name : $feed->set_title,
            'company' => empty($feed->set_title) ? $trader->name : $feed->set_title,
            'description' => empty($feed->set_description) ? $trader->description : $feed->set_description,
            'url' => request()->host(),
            'preprice' => $feed->set_preprice,
        ];
    }


}
