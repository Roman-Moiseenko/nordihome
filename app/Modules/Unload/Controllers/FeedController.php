<?php

namespace App\Modules\Unload\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Unload\Entity\Feed;
use App\Modules\Unload\Repository\FeedRepository;
use App\Modules\Unload\Service\FeedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    private FeedService $service;
    private FeedRepository $repository;


    public function __construct(FeedService $service,
                                FeedRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $feeds = $this->repository->getIndex($request, $filters);

        return Inertia::render('Unload/Feed/Index', [
            'feeds' => $feeds,
            'filters' => $filters,
        ]);
    }

    public function show(Feed $feed): Response
    {
        $tags = Tag::orderBy('name')->get()->toArray();
        return Inertia::render('Unload/Feed/Show', [
            'feed' => fn() => $this->repository->FeedToArray($feed),
            'tags' => $tags,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $feed = $this->service->create($request);
        return redirect()->route('admin.unload.feed.show', $feed)->with('success', 'Фид создан');
    }

    public function set_info(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->setInfo($feed, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
    public function toggle(Feed $feed): RedirectResponse
    {
        $message = $this->service->toggle($feed);
        return redirect()->back()->with('success', $message);
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $this->service->delete($feed);
        return redirect()->back()->with('success', 'Фид удален');
    }

    public function add_product(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->addProduct($feed, $request);
        return redirect()->back()->with('success', 'Добавлено');
    }

    public function add_products(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->addProducts($feed, $request);
        return redirect()->back()->with('success', 'Добавлено');
    }

    public function del_product(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->delProduct($feed, $request);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function del_products(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->delProducts($feed, $request);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function add_tag(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->addTag($feed, $request);
        return redirect()->back()->with('success', 'Добавлено');
    }

    public function del_tag(Feed $feed, Request $request): RedirectResponse
    {
        $this->service->delTag($feed, $request);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function categories(Feed $feed, Request $request)
    {
        $this->service->setCategories($feed, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }


}
