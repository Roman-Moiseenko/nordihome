<?php

namespace App\Modules\Unload\Service;

use App\Modules\Unload\Entity\Feed;
use Illuminate\Http\Request;

class FeedService
{
    public function create(Request $request): Feed
    {
        return Feed::register($request->string('name')->trim()->value());
    }

    public function setInfo(Feed $feed, Request $request): void
    {
        $feed->set_preprice = $request->boolean('set_preprice');
        $feed->set_title = $request->string('set_title')->trim()->value();
        $feed->set_description = $request->string('set_description')->trim()->value();
        $feed->save();
    }

    public function toggle(Feed $feed): string
    {
        $message = $feed->active ? 'Фид больше не доступен' : 'Фид доступен для маркетов';

        $feed->active = !$feed->active;
        $feed->save();
        return $message;
    }

    public function delete(Feed $feed): void
    {
        if ($feed->active) throw new \DomainException('Нельзя удалить активный фид');
        $feed->delete();
    }

    public function addProduct(Feed $feed, Request $request): void
    {
        $id = $request->integer('product_id');
        if ($id == 0) throw new \DomainException('Товар не выбран');
        $in = $request->boolean('in');
        $product_ids = $in ? $feed->products_in : $feed->products_out;
        if (in_array($id, $product_ids)) throw new \DomainException('Товар уже добавлен');
        $product_ids[] = $id;
        if ($in) {
            $feed->products_in = $product_ids;
        } else {
            $feed->products_out = $product_ids;
        }
        $feed->save();
    }

    public function addProducts(Feed $feed, Request $request): void
    {
        $products = $request->input('products');
        $in = $request->boolean('in');
        $product_ids = $in ? $feed->products_in : $feed->products_out;

        foreach ($products as $product) {
            $product_ids[] = $product['product_id'];
        }
        $product_ids = array_unique($product_ids);
        if ($in) {
            $feed->products_in = $product_ids;
        } else {
            $feed->products_out = $product_ids;
        }
        $feed->save();
    }

    public function delProduct(Feed $feed, Request $request): void
    {
        $id = $request->integer('product_id');
        $products_in = $feed->products_in;
        foreach ($products_in as $key => $product_id) {
            if ($product_id == $id) {
                unset($products_in[$key]);
                $feed->products_in = $products_in;
                $feed->save();
                return;
            }
        }
        ///
        $products_out = $feed->products_out;
        foreach ($products_out as $key => $product_id) {
            if ($product_id == $id) {
                unset($products_out[$key]);
                $feed->products_out = $products_out;
                $feed->save();
                return;
            }
        }
    }

    public function delProducts(Feed $feed, Request $request): void
    {
        if ($request->boolean('in')) {
            $feed->products_in = [];
        } else {
            $feed->products_out = [];
        }
        $feed->save();
    }

    public function addTag(Feed $feed, Request $request): void
    {
        $id = $request->integer('tag_id');
        if ($id == 0) throw new \DomainException('Метка не выбрана');
        if (in_array($id, $feed->tags_in) || in_array($id, $feed->tags_out))
            throw new \DomainException('Метка уже добавлена');

        $in = $request->boolean('tag_in');
        $tags = $in ? $feed->tags_in : $feed->tags_out;

        $tags[] = $id;

        if ($in) {
            $feed->tags_in = $tags;
        } else {
            $feed->tags_out = $tags;
        }
        $feed->save();
    }

    public function delTag(Feed $feed, Request $request): void
    {
        $id = $request->integer('tag_id');

        $tag_in = $feed->tags_in;
        foreach ($tag_in as $key => $tag_id) {
            if ($tag_id == $id) {
                unset($tag_in[$key]);
                $feed->tags_in = $tag_in;
                $feed->save();
                return;
            }
        }
        $tags_out = $feed->tags_out;

        foreach ($tags_out as $key => $tag_id) {
            if ($tag_id == $id) {
                unset($tags_out[$key]);
                $feed->tags_out = $tags_out;
                $feed->save();
                return;
            }
        }

    }

    public function setCategories(Feed $feed, Request $request): void
    {
        $categories = $request->input('categories') ?? [];
        if ($request->boolean('in')) {
            $feed->categories_in = $categories;
        } else {
            $feed->categories_out = $categories;
        }
        $feed->save();
    }
}
