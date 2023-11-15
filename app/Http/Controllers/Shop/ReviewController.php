<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;

class ReviewController extends Controller
{

    public function index()
    {
        return redirect()->route('home');
    }
}
