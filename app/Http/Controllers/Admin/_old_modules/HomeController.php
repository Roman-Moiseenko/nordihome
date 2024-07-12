<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules;

use App\Http\Controllers\Controller;
use function view;


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
