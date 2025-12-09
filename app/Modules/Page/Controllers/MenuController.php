<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Menu;
use App\Modules\Page\Entity\MenuItem;
use App\Modules\Page\Repository\MenuRepository;
use App\Modules\Page\Service\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    private MenuService $service;
    private MenuRepository $repository;

    public function __construct(
        MenuService    $service,
        MenuRepository $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $menus = $this->repository->getMenus($request);

        return Inertia::render('Page/Menu/Index', [
            'page_menus' => $menus,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->createMenu($request);
        return redirect()->route('admin.page.menu.index')->with('success', 'Меню добавлено');
    }

    public function item_move(Menu $menu, Request $request): RedirectResponse
    {
        $this->service->moveItems($menu, $request);
        $menu->refresh();
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function item_add(Menu $menu, Request $request): RedirectResponse
    {
        $this->service->addItem($menu, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function item_set(MenuItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function item_delete(MenuItem $item): RedirectResponse
    {
        $this->service->deleteItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->service->deleteMenu($menu);
        return redirect()->route('admin.page.menu.index')->with('success', 'Меню удалено');
    }

    public function set_info(Menu $menu, Request $request): RedirectResponse
    {
        $this->service->setInfo($menu, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function get_urls(Menu $menu): JsonResponse
    {
        $urls = $this->repository->getUrls($menu);
        //$urls['id'] = $menu->items()->get()->map(fn(MenuItem $item) => $item->url);
        return \response()->json($urls);
    }

    public function get_items(Menu $menu): JsonResponse
    {
        $items = $this->repository->getItems($menu);
        return \response()->json($items);
    }

}
