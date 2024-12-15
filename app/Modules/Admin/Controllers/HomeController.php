<?php
declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Base\Helpers\Version;
use Inertia\Inertia;


class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
       // return view('admin.home', ['layout' => 'admin']);
        $updates = Version::updated();
        $version = Version::VERSION;

        return Inertia::render('Admin/Home', [
            'updates' => $updates,
            'version' => $version,
        ]);
    }
}
