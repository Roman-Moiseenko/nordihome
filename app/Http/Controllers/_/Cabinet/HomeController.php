<?php
declare(strict_types=1);

namespace App\Http\Controllers\_\Cabinet;

use App\Http\Controllers\Controller;
use function view;

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
