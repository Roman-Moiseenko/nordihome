<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return view('shop.catalog.room', ['rooms' => []]);
    }

    public function view(Request $request, string $slug)
    {
        $page = $request->has('page');
        return view('shop.catalog.room', ['room' => []]);
    }
}
