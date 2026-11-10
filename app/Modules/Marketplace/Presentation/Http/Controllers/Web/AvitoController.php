<?php

namespace App\Modules\Marketplace\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AvitoController extends Controller
{
    public function __construct() {}

    public function index(Request $request, UserPermission $userPermission)
    {

        return Inertia::render('Marketplace/Avito/Index', [

        ]);
    }
}
