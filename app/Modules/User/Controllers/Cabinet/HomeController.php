<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('cabinet.home');
    }
}
