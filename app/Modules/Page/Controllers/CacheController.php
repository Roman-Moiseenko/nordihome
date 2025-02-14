<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CacheController extends Controller
{
    public function index(Request $request): Response
    {
        $caches = [];
        //$templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Cache/Index', [
            'caches' => $caches,
        ]);
    }
}
