<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Service\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CacheController extends Controller
{
    private CacheService $service;

    public function __construct(CacheService $service)
    {
        $this->service = $service;
    }

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
        $count = $this->service->rebuildCache();
        return redirect()->back()->with('success', 'Кеш запущен в обработку: Товаров - ' . $count['products'] . ' Категорий - ' . $count['categories']);
    }

    public function categories(): RedirectResponse
    {
        $count = $this->service->rebuildCategories();
        return redirect()->back()->with('success', 'Кеш запущен в обработку: Категорий - ' . $count);
    }

    public function products(): RedirectResponse
    {
        $count = $this->service->rebuildProducts();
        return redirect()->back()->with('success', 'Кеш запущен в обработку: Товаров - ' . $count);
    }

    public function clear(): RedirectResponse
    {
        $this->service->clearAll();
        return redirect()->back()->with('success', 'Кеш очищен');
    }
}
