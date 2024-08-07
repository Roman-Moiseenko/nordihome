<?php
declare(strict_types=1);

namespace App\Modules\Setting\Repository;


use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Coupon;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Setting;

use App\Modules\Setting\Entity\Web;
use Illuminate\Contracts\Support\Arrayable;

class SettingRepository
{

    public function getIndex()
    {
        return Setting::orderBy('name')->getModels();
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
}
