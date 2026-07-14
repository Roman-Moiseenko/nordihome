<?php

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Repository\PostRepository;
use App\Modules\Content\Repository\PostWidgetRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\PostWidgetService;
use App\Modules\Content\Entity\Widgets\PostWidget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostWidgetController extends Controller
{
    private TemplateRepository $templates;
    private PostWidgetRepository $repository;
    private PostWidgetService $service;
    private PostRepository $postRepository;

    public function __construct(
        PostWidgetService    $service,
        TemplateRepository     $templates,
        PostWidgetRepository $repository,
        PostRepository $postRepository,
    )
    {
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
        $this->postRepository = $postRepository;
    }

    public function index(Request $request): Response
    {
        $widgets = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('post_widget');

        return Inertia::render('Content/Widget/Post/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.content.widget.post.show', $widget)->with('success', 'Виджет создан');
    }

    public function show(PostWidget $widget, Request $request): Response
    {
        $templates = $this->templates->getTemplates('post_widget');
        $categories = $this->postRepository->getCategories($request);

        return Inertia::render('Content/Widget/Post/Show', [
            'widget' => $this->repository->PostWithToArray($widget),
            'templates' => $templates,
            'categories' => $categories,
        ]);
    }

    public function set_widget(PostWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
    public function toggle(PostWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }
    public function destroy(PostWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Виджет удален');
    }
}
