<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageService
{

    public function create(Request $request): Page
    {
        return Page::register(
            $request->string('name')->trim()->value(),
            $request->string('slug')->trim()->value(),
            $request->string('title')->trim()->value(),
            $request->string('description')->trim()->value(),
            $request->string('template')->trim()->value(),
            $request->has('menu'),
            $request['parent_id'],
        );
    }

    public function update(Request $request, Page $page)
    {
        $page->update([
            'name' => $request->string('name')->trim()->value(),
            'slug' => $request->string('slug')->trim()->value(),
            'title' => $request->string('title')->trim()->value(),
            'description' => $request->string('description')->trim()->value(),
            'template' => $request->string('template')->trim()->value(),
            'menu' => $request->has('menu'),
            'parent_id' => $request['parent_id'],
        ]);
    }

    public function destroy(Page $page): void
    {
        if ($page->published) throw new \DomainException('страница опубликована');
        $page->delete();
    }

    public function setText(Page $page, string $text): void
    {
        $page->text = $text;
        $page->save();
    }

    public function setInfo(Page $page, Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        $page->name = $name;
        $page->slug = empty($slug) ? Str::slug($name) : $slug;
        $page->title = $request->string('title')->trim()->value();
        $page->description = $request->string('description')->trim()->value();
        $page->parent_id = $request->input('parent_id');
        $page->menu = $request->boolean('menu');
        $page->template = $request->string('template')->value();
        $page->meta->fromRequest($request);
        $page->save();

        $page->saveImage($request->file('image'), $request->boolean('clear_image'));
        $page->saveIcon($request->file('icon'), $request->boolean('clear_icon'));
    }

    public function up(Page $page): void
    {
        $pages = Page::orderBy('sort')->where('parent_id', $page->parent_id)->getModels();
        for ($i = 1; $i < count($pages); $i++) {
            if ($pages[$i]->id == $page->id) {
                $prev = $pages[$i - 1]->sort;
                $next = $pages[$i]->sort;
                $pages[$i]->update(['sort' => $prev]);
                $pages[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function down(Page $page): void
    {
        $pages = Page::orderBy('sort')->where('parent_id', $page->parent_id)->getModels();
        for ($i = 0; $i < count($pages) - 1; $i++) {
            if ($pages[$i]->id == $page->id) {
                $prev = $pages[$i + 1]->sort;
                $next = $pages[$i]->sort;
                $pages[$i]->update(['sort' => $prev]);
                $pages[$i + 1]->update(['sort' => $next]);
            }
        }
    }
}
