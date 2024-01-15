<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Modules\Pages\Entity\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{

    public function view($slug)
    {
        $page = Page::where('slug', $slug)->first();

        return $page->view();
    }


}
