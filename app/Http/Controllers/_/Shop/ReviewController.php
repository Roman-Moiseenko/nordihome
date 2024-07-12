<?php
declare(strict_types=1);

namespace App\Http\Controllers\_\Shop;

use App\Http\Controllers\Controller;
use function redirect;

class ReviewController extends Controller
{

    public function index()
    {
        return redirect()->route('shop.home');
    }
}
