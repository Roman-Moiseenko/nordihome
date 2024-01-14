<?php
declare(strict_types=1);

namespace App\Modules\Pages\Service;

use App\Modules\Pages\Entity\Page;
use Illuminate\Http\Request;

class PageService
{

    public function create(Request $request)
    {
        $page = Page::register();

        return $page;
    }

    public function update(Request $request, Page $page)
    {
        return $page;
    }
    public function destroy(Page $page)
    {

    }



}
