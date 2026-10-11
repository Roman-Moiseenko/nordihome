<?php
declare(strict_types=1);

namespace App\Modules\Shared\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Base\Helpers\Version;
use Inertia\Inertia;


class HomeAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       // return view('admin.home', ['layout' => 'admin']);
        $updates = Version::updated();
        $version = Version::VERSION;

        return Inertia::render('Home', [
            'updates' => $updates,
            'version' => $version,
        ]);
    }
}
