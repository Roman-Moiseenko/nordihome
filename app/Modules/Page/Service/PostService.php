<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostService
{

    public function createCategory(Request $request): PostCategory
    {
        return PostCategory::register(
            $request->string('name')->trim()->value(),
            $request->string('slug')->trim()->value(),
            $request->string('template')->trim()->value(),
        );
    }

    public function setInfoCategory(PostCategory $category, Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        $category->name = $name;
        $category->slug  = empty($slug) ? Str::slug($name) : $slug;
        $category->template = $request->string('template')->value();
        $category->post_template = $request->string('post_template')->value();
        $category->title = $request->string('title')->trim()->value();
        $category->description = $request->string('description')->trim()->value();
        $category->paginate = $request->input('paginate');
        $category->meta->fromRequest($request);
        $category->save();

        $category->saveImage($request->file('image'), $request->boolean('clear_image'));
        $category->saveIcon($request->file('icon'), $request->boolean('clear_icon'));
    }

    public function destroyCategory(PostCategory $category): void
    {
        if ($category->posts()->count() != 0) throw new \DomainException('Нельзя удалить! Есть записи');
        $category->delete();
    }

    public function createPost(PostCategory $category, Request $request): Post
    {
        $post = Post::new(
            $request->string('name')->trim()->value(),
            $request->string('template')->trim()->value()
        );
        //$post->category_id = $category->id;
        //$post->save();
        $category->posts()->save($post);
        $category->refresh();
        return $post;
    }

    public function togglePost(Post $post): string
    {
        if ($post->published) {
            $message = 'Запись снята с публикации';
            $post->draft();
        } else {
            $message = 'Запись опубликована';
            $post->published();
        }
        $post->save();
        return $message;
    }

    public function setTextPost(Post $post, Request $request): void
    {
        $post->text = $request->string('text')->trim()->value();
        $post->save();
    }

    public function setInfoPost(Post $post, Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        $post->name = $name;
        $post->slug  = empty($slug) ? Str::slug($name) : $slug;
        $post->template = $request->string('template')->value();
        $post->title = $request->string('title')->trim()->value();
        $post->description = $request->string('description')->trim()->value();
        $post->published_at = $request->input('published_at');
        $post->meta->fromRequest($request);
        $post->save();

        $post->saveImage($request->file('image'), $request->boolean('clear_image'));
        $post->saveIcon($request->file('icon'), $request->boolean('clear_icon'));

    }
}
