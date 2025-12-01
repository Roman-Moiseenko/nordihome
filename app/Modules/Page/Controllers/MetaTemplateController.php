<?php

namespace App\Modules\Page\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Page\Repository\MetaTemplateRepository;
use App\Modules\Page\Service\MetaTemplateService;
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
        return Inertia::render('Page/SEO/Meta/Index', [
            'templates' => $templates,
        ]);
    }

    public function set_data(Request $request): RedirectResponse
    {
        $this->service->setData($request);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
