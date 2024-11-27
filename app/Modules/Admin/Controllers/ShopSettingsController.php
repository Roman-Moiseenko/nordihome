<?php
declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use App\Modules\Admin\Service\SettingService;
use Illuminate\Http\Request;
use function redirect;
use function view;


class ShopSettingsController extends Controller
{
    private SettingService $service;

    public function __construct(SettingService $service)
    {
        //$this->middleware(['auth:admin, can:options']);
        $this->service = $service;
    }

    public function index()
    {
        $setting = Setting::where('slug', 'shop')->first();
        $items = SettingItem::orderBy('sort')->where('setting_id', $setting->id)->getModels();
        $groups = [];

        /** @var SettingItem $item */
        foreach ($items as $item) {
            $groups[$item->tab][] = $item;
        }
        return view('admin.settings.shop', compact('groups', 'setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::where('slug', 'shop')->first();
        $this->service->set($request, $setting);
        return redirect()->route('admin.settings.shop');
    }
}
