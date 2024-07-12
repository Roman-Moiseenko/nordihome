<?php
declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;


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
