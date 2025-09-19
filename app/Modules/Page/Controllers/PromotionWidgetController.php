<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Repository\PromotionRepository;
use App\Modules\Page\Repository\TemplateRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromotionWidgetController extends Controller
{

    private TemplateRepository $templates;
    private PromotionRepository $repository;

    public function __construct(
        PromotionRepository $repository,
        TemplateRepository $templates,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);

        $this->templates = $templates;
        $this->repository = $repository;
    }


    public function index(Request $request): Response
    {
        $promotions = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Promotion/Index', [
            'promotions' => $promotions,
            'templates' => $templates,
        ]);
    }
}
