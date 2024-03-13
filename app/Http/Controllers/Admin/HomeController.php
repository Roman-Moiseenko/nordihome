<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return $this->try_catch_admin(function () {
            return view('admin.home', ['layout' => 'admin']);
        });
    }
}
