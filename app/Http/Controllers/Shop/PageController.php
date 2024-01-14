<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{

    public function view($slug)
    {
        return view('shop.pages.' . $slug);
    }


}
