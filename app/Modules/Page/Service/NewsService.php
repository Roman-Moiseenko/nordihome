<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\News;
use Illuminate\Http\Request;

class NewsService
{

    public function toggle(News $news): void
    {
        if (!$news->isPublished()) $news->published_at = now();
        $news->published = !$news->published;
        $news->save();
    }

    public function create(Request $request): News
    {
        $news = News::register(
            $request->string('title')->trim()->value(),
            $request->string('text')->trim()->value());
        $news->saveImage($request->file('file'));
       // $news->save();
        return $news;
    }

    public function update(News $news, Request $request): void
    {
        $news->title = $request->string('title')->trim()->value();
        $news->text = $request->string('text')->trim()->value();
        $news->published_at = $request->input('published_at');
        $news->save();
        $news->saveImage($request->file('file'), $request->boolean('clear_file'));
    }

    public function destroy(News $news): void
    {
        if (!is_null($news->image)) $news->image->delete();
        $news->delete();
    }
}
