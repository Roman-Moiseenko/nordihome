<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\BannerWidgetItem;
use App\Modules\Page\Entity\Template;
use App\Modules\Page\Repository\BannerWidgetRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\BannerWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerWidgetController extends Controller
{
    private TemplateRepository $templates;
    private BannerWidgetRepository $repository;
    private BannerWidgetService $service;

    public function __construct(
        BannerWidgetService    $service,
        TemplateRepository     $templates,
        BannerWidgetRepository $repository,
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

        return Inertia::render('Page/Widget/Banner/Index', [
            'banners' => $banners,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $banner = $this->service->create($request);
        return redirect()->route('admin.page.widget.banner.show', $banner)->with('success', 'Баннер сохранен');
    }

    public function show(BannerWidget $banner): Response
    {
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Widget/Banner/Show', [
            'banner' => $this->repository->BannerWithToArray($banner),
            'templates' => $templates,
        ]);
    }

    public function set_banner(BannerWidget $banner, Request $request): RedirectResponse
    {
        $this->service->setBanner($banner, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(BannerWidget $banner): RedirectResponse
    {
        $this->service->delBanner($banner);
        return redirect()->back()->with('success', 'Баннер удален');
    }

    public function add_item(BannerWidget $banner, Request $request): RedirectResponse
    {
        $this->service->addItem($banner, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(BannerWidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(BannerWidget $banner): RedirectResponse
    {
        if ($banner->isActive()) {
            $message = 'Баннер убран из показа';
        } else {
            $message = 'Баннер добавлен в показы';
        }
        $this->service->toggle($banner);
        return redirect()->back()->with('success', $message);
    }

    public function up_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
