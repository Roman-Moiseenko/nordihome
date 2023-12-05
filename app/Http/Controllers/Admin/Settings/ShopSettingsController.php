<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Settings;

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
        $this->middleware(['auth:admin']);
        $this->service = $service;
    }

    public function index()
    {
        $setting = Setting::where('slug', 'shop')->first();
        $items = SettingItem::where('setting_id', $setting->id)->get();
        return view('admin.settings.shop', compact('items', 'setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::where('slug', 'shop')->first();
        $this->service->set($request, $setting);
        return redirect()->route('admin.settings.shop');
    }
}
