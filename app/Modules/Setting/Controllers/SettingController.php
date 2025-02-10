<?php
declare(strict_types=1);

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Setting\Service\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    private SettingService $service;
    private SettingRepository $repository;
    private Settings $settings;

    public function __construct(SettingService $service, SettingRepository $repository, Settings $settings)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->settings = $settings;
    }

    public function index(Request $request): Response
    {
        $settings = $this->repository->getIndex($request);
        return Inertia::render('Setting/Index', [
                'settings' => $settings,
            ]
        );
    }

    public function common(): Response
    {
        $groups = Group::orderBy('name')->get()->toArray();
        return Inertia::render('Setting/Common', [
                'common' => $this->settings->common,
                'groups' => $groups,
            ]
        );
    }

    public function coupon(): Response
    {
        return Inertia::render('Setting/Coupon', [
                'coupon' => $this->settings->coupon,
            ]
        );
    }

    public function parser(): Response
    {
        return Inertia::render('Setting/Parser', [
                'parser' => $this->settings->parser,
            ]
        );
    }

    public function web(): Response
    {
        return Inertia::render('Setting/Web', [
                'web' => $this->settings->web,
            ]
        );
    }

    //Настройки почты
    public function mail(): Response
    {
        return Inertia::render('Setting/Mail', [
                'mail' => $this->settings->mail,
            ]
        );
    }

    //Настройки Уведомлений
    public function notification()
    {
        return Inertia::render('Setting/Notification', [
                'notification' => $this->settings->notification,
            ]
        );
    }

    //Изображения на сайт
    public function image(): Response
    {
        return Inertia::render('Setting/Image', [
                'image' => $this->settings->image,
            ]
        );
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $this->service->update($request);
            return redirect()->back()->with('success', 'Сохранение прошло успешно');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
