<?php

namespace App\Modules\Nordihome\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('NBRussia/Test/Index', [

        ]);
    }
}
