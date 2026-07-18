<?php

namespace App\Modules\Content\Repository;

use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Infrastructure\Models\Post;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PostRepository
{

    public function getCategories(Request $request): Arrayable
    {
        return PostCategory::orderBy('title')->get()->map(fn (PostCategory $category) => $this->CategoryToArray($category));;
    }

    public function CategoryWithToArray(PostCategory $category): array
    {
        return array_merge($category->toArray(), [
            'image' => $category->getImage(),
            'icon' => $category->getIcon(),
            'meta' => $category->meta->toArray(),
            'posts' => $category->posts()->get()->map(fn(Post $post) => $this->PostWithToArray($post)),
        ]);
    }

    private function CategoryToArray(PostCategory $category): array
    {
        return array_merge($category->toArray(), [
            'posts' => $category->posts()->count(),
        ]);
    }

    public function PostWithToArray(Post $post): array
    {
        return array_merge($post->toArray(), [
            'image' => $post->getImage(),
            'meta' => $post->meta,
        ]);
    }


}
