<?php
declare(strict_types=1);

namespace App\Modules\Setting\Repository;

use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Coupon;
use App\Modules\Setting\Entity\Mail;
use App\Modules\Setting\Entity\Notification;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Setting;

use App\Modules\Setting\Entity\Web;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SettingRepository
{

    public function getIndex(Request $request): Arrayable
    {
        return Setting::orderBy('name')
            ->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Setting $setting) => [
                'id' => $setting->id,
                'name' => $setting->name,
                'slug' => $setting->slug,
                'class' => $setting->class,
                'description' => $setting->description,
            ]);
    }

    public function getCommon(): Common
    {
        $setting = Setting::where('slug', 'common')->first();
        return new Common($setting->getData());
    }

    public function getCoupon(): Coupon
    {
        $setting = Setting::where('slug', 'coupon')->first();
        return new Coupon($setting->getData());
    }

    public function getParser(): Parser
    {
        $setting = Setting::where('slug', 'parser')->first();
        return new Parser($setting->getData());
    }

    public function getWeb(): Web
    {
        $setting = Setting::where('slug', 'web')->first();
        return new Web($setting->getData());
    }

    public function getMail(): Mail
    {
        $setting = Setting::where('slug', 'mail')->first();
        return new Mail($setting->getData());
    }

    public function getNotification(): Notification
    {
        $notification = Setting::where('slug', 'notification')->first();
        return new Notification($notification->getData());
    }
}
