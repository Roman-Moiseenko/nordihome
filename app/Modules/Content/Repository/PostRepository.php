<?php

namespace App\Modules\Content\Repository;

use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Content\Infrastructure\Models\PostCategory;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;
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
            'image' => GetPhotoStatic::get('content.post-category', $category->id),
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
            'image' => GetPhotoStatic::get('content.post', $post->id),
            'meta' => $post->meta,
        ]);
    }


}
