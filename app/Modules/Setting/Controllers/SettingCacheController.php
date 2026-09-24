<?php

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingCacheController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('Setting/Cache/Index', [
            ]
        );
    }

    public function clearAll()
    {

    }

    public function clearImage()
    {

    }

    public function recache()
    {

    }
}
