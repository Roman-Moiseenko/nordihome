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
        if (is_null($setting)) $setting = $this->createCommon();
        return new Common($setting->getData());
    }

    public function getCoupon(): Coupon
    {
        $setting = Setting::where('slug', 'coupon')->first();
        if (is_null($setting)) $setting = $this->createCoupon();
        return new Coupon($setting->getData());
    }

    public function getParser(): Parser
    {
        $setting = Setting::where('slug', 'parser')->first();
        if (is_null($setting)) $setting = $this->createParser();
        return new Parser($setting->getData());
    }

    public function getWeb(): Web
    {
        $setting = Setting::where('slug', 'web')->first();
        if (is_null($setting)) $setting = $this->createWeb();
        return new Web($setting->getData());
    }

    public function getMail(): Mail
    {
        $setting = Setting::where('slug', 'mail')->first();
        if (is_null($setting)) $setting = $this->createMail();
        return new Mail($setting->getData());
    }

    public function getNotification(): Notification
    {
        $setting = Setting::where('slug', 'notification')->first();
        if (is_null($setting)) $setting = $this->createNotification();
        return new Notification($setting->getData());
    }

    private function createCommon(): Setting
    {
        return Setting::register(
            'Общие настройки',
            'common',
            'Общие настройки торговой компании, товарный учет, время резерва товара и т.п.');
    }

    private function createCoupon(): Setting
    {
        return Setting::register(
            'Скидочные купоны',
            'coupon',
            'Настройка скидочных купонов - время действия, минимальная сумма, подарочные супоны и др.',
        );
    }

    private function createParser(): Setting
    {
        return Setting::register(
            'Настройки парсера',
            'parser',
            'Стоимость доставки, прокси данные для парсера, коэффициент доставки и др.',
        );
    }

    private function createWeb(): Setting
    {
        return Setting::register(
            'Настройки сайта',
            'web',
            'Общие настройки главных цветов, подвала и шапки сайта, логотип, отображения товаров и др.',
        );
    }

    private function createMail(): Setting
    {
        return Setting::register(
            'Настройки почты',
            'mail',
            'Настройка входящей почты - почтовые ящики (+пароли), с которых необходимо собирать почту, Настройки исходящей и системной почты',
        );
    }

    private function createNotification(): Setting
    {
        return Setting::register(
            'Настройки уведомлений',
            'notification',
            'API доступы и ключи к мессенджерам, настройки уведомлений'
        );
    }
}
