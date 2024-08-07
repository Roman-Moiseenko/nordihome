<?php
declare(strict_types=1);

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Setting\Service\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private SettingService $service;
    private SettingRepository $repository;

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }


    public function index(Request $request)
    {
        $settings = $this->repository->getIndex($request);
        return view('admin.settings.index', compact('settings'));
    }

    public function common()
    {
        $common = $this->repository->getCommon();
        return view('admin.settings.common', compact('common'));

    }

    public function coupon()
    {
        $coupon = $this->repository->getCoupon();
        return view('admin.settings.coupon', compact('coupon'));
    }

    public function parser()
    {
        $parser = $this->repository->getParser();
        return view('admin.settings.parser', compact('parser'));
    }

    public function web()
    {
        $web = $this->repository->getWeb();
        return view('admin.settings.web', compact('web'));
    }


    public function update(Request $request)
    {
        $this->service->update($request);
        flash('Сохранение прошло успешно');
        return redirect()->back()->with('success', 'Сохранение прошло успешно');
    }
}
