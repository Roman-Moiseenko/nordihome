<?php
declare(strict_types=1);

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
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

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
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
        $common = $this->repository->getCommon();
        $groups = Group::orderBy('name')->get()->toArray();
        return Inertia::render('Setting/Common', [
                'common' => $common,
                'groups' => $groups,
            ]
        );
    }

    public function coupon(): Response
    {
        $coupon = $this->repository->getCoupon();
        return Inertia::render('Setting/Coupon', [
                'coupon' => $coupon,
            ]
        );
    }

    public function parser(): Response
    {
        $parser = $this->repository->getParser();
        return Inertia::render('Setting/Parser', [
                'parser' => $parser,
            ]
        );
    }

    public function web(): Response
    {
        $web = $this->repository->getWeb();
        return Inertia::render('Setting/Web', [
                'web' => $web,
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
