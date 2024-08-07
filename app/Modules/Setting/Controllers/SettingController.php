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
        return $common->view();
    }

    public function coupon()
    {
        $coupon = $this->repository->getCoupon();
        return $coupon->view();
    }

    public function parser()
    {
        $parser = $this->repository->getParser();
        return $parser->view();
    }

    public function web()
    {
        $web = $this->repository->getWeb();
        return $web->view();
    }


    public function update(Request $request)
    {
        $this->service->update($request);
        flash('Сохранение прошло успешно');
        return redirect()->back()->with('success', 'Сохранение прошло успешно');
    }
}
