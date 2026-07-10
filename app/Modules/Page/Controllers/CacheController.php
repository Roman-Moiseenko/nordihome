<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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

    public function create(): RedirectResponse
    {
        //MAINDO Пересобрать кэш

        return redirect()->back()->with('success', 'Кеш запущен в обработку');    }

    public function categories(): RedirectResponse
    {
        //MAINDO Пересобрать кэш

        return redirect()->back()->with('success', 'Кеш запущен в обработку');
    }

    public function products(): RedirectResponse
    {
        //MAINDO Пересобрать кэш

        return redirect()->back()->with('success', 'Кеш запущен в обработку');
    }

    public function clear(): RedirectResponse
    {
        //MAINDO Очистить кэш

        return redirect()->back()->with('success', 'Кеш очищен');
    }

    public function pages(): RedirectResponse
    {
        //MAINDO Пересобрать кэш

        return redirect()->back()->with('success', 'Кеш запущен в обработку');
    }
}
