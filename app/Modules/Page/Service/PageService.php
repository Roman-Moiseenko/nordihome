<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Page;
use Illuminate\Http\Request;

class PageService
{

    public function create(Request $request): Page
    {
        $page = Page::register(
            $request['name'],
            $request['slug'],
            $request['title'],
            $request['description'] ?? '',
            $request['template'],
            $request->has('menu'),
            $request['parent_id'],
        );
        return $page;
    }

    public function update(Request $request, Page $page): Page
    {
        $page->update([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'title' => $request['title'],
            'description' => $request['description'] ?? '',
            'template' => $request['template'],
            'menu' => $request->has('menu'),
            'parent_id' => $request['parent_id'],
        ]);
        return $page;
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
