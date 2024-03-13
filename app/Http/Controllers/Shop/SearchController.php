<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class SearchController extends Controller
{

    public function index()
    {
        return redirect()->route('home');
    }
}
