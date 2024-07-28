<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Page;
use Illuminate\Http\Request;

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

    public function destroy(Page $page)
    {
        $page->delete();
    }

    public function setText(Request $request, Page $page): Page
    {
        $text = $request['text'];
        $page->setText($text);
        return $page;
    }

}
