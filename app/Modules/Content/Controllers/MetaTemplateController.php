<?php

namespace App\Modules\Content\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Content\Repository\MetaTemplateRepository;
use App\Modules\Content\Service\MetaTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MetaTemplateController extends Controller
{

    private MetaTemplateRepository $repository;
    private MetaTemplateService $service;

    public function __construct(MetaTemplateRepository $repository, MetaTemplateService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $templates = $this->repository->getIndex($request);
        return Inertia::render('Content/SEO/Meta/Index', [
            'templates' => $templates,
        ]);
    }

    public function set_data(Request $request): RedirectResponse
    {
        $this->service->setData($request);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
