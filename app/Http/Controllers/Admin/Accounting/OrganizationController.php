<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:accounting']);
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {

        });
    }
}
