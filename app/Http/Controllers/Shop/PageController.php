<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Page\Entity\Page;
use Illuminate\Routing\Controller;

class PageController extends Controller
{

    public function view($slug)
    {
        $page = Page::where('slug', $slug)->first();

        return $page->view();
    }


}
