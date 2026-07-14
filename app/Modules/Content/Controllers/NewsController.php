<?php

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Entity\News;
use App\Modules\Content\Repository\NewsRepository;
use App\Modules\Content\Service\NewsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    private NewsService $service;
    private NewsRepository $repository;

    public function __construct(NewsService $service, NewsRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $news = $this->repository->getIndex($request);

        return Inertia::render('Content/News/Index', [
            'news' => $news,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $news = $this->service->create($request);
        return redirect()->back()->with('message', 'Новость добавлена! Опубликуйте ее!');
    }


    public function toggle(News $news, Request $request): RedirectResponse
    {
        $message = $news->isPublished() ? 'Новость убрана из показа' : 'Новость опубликована';

        $this->service->toggle($news);
        return redirect()->back()->with('success', $message);
    }

    public function update(News $news, Request $request): RedirectResponse
    {
        $this->service->update($news, $request);
        return redirect()->back()->with('message', 'Сохранено!');
    }

    public function destroy(News $news)
    {
        $this->service->destroy($news);
        return redirect()->back()->with('message', 'Новость удалена!');

    }

}
