<?php
declare(strict_types=1);

namespace App\Modules\Shop\Controllers;

use App\Http\Controllers\Controller;

class SearchController extends Controller
{

    public function index()
    {
        return redirect()->route('shop.home');
    }
}
