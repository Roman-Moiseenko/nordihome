<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\Template;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\BannerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    private TemplateRepository $templates;

    public function __construct(BannerService $service, TemplateRepository $templates)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
    }

    public function index(Request $request): \Inertia\Response
    {
        $banners = Banner::orderBy('name')->get();
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Banner/Index', [
            'banners' => $banners,
            'templates' => $templates,
        ]);
    }
}
