<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\BannerItem;
use App\Modules\Page\Entity\Template;
use App\Modules\Page\Repository\BannerRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    private TemplateRepository $templates;
    private BannerRepository $repository;

    public function __construct(
        BannerService      $service,
        TemplateRepository $templates,
        BannerRepository   $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $banners = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Banner/Index', [
            'banners' => $banners,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $banner = $this->service->create($request);
        return redirect()->route('admin.page.banner.show', $banner)->with('success', 'Баннер сохранен');
    }

    public function show(Banner $banner): Response
    {
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Banner/Show', [
            'banner' => $this->repository->BannerWithToArray($banner),
            'templates' => $templates,
        ]);
    }

    public function set_banner(Banner $banner, Request $request): RedirectResponse
    {
        $this->service->setBanner($banner, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->service->delBanner($banner);
        return redirect()->back()->with('success', 'Баннер удален');
    }

    public function add_item(Banner $banner, Request $request): RedirectResponse
    {
        $this->service->addItem($banner, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(BannerItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(BannerItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        if ($banner->isActive()) {
            $message = 'Баннер убран из показа';
        } else {
            $message = 'Баннер добавлен в показы';
        }
        $this->service->toggle($banner);
        return redirect()->back()->with('success', $message);
    }

    public function up_item(BannerItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(BannerItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
