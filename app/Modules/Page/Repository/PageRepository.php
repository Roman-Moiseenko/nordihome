<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Page;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PageRepository
{

    public function getIndex(Request $request): Arrayable
    {
        return Page::orderBy('sort')->get()->map(fn (Page $page) => $this->PageToArray($page));
    }

    private function PageToArray(Page $page): array
    {
        return array_merge($page->toArray(), [
            'parent_name' => is_null($page->parent_id) ? null : $page->parent->name,

        ]);
    }
    public function PageWithToArray(Page $page): array
    {
        return array_merge($this->PageToArray($page), [
            'image' => $page->getImage(),
            'icon' => $page->getIcon(),
        ]);
    }
}
