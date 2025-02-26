<?php
declare(strict_types=1);

namespace App\Modules\Setting\Repository;

use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Coupon;
use App\Modules\Setting\Entity\Image;
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
        return Common::create($setting->getData());
    }

    public function getCoupon(): Coupon
    {
        $setting = Setting::where('slug', 'coupon')->first();
        if (is_null($setting)) $setting = $this->createCoupon();
        return Coupon::create($setting->getData());
    }

    public function getParser(): Parser
    {
        $setting = Setting::where('slug', 'parser')->first();
        if (is_null($setting)) $setting = $this->createParser();
        return Parser::create($setting->getData());
    }

    public function getWeb(): Web
    {
        $setting = Setting::where('slug', 'web')->first();
        if (is_null($setting)) $setting = $this->createWeb();
        return Web::create($setting->getData());
    }

    public function getMail(): Mail
    {
        $setting = Setting::where('slug', 'mail')->first();
        if (is_null($setting)) $setting = $this->createMail();
        return Mail::create($setting->getData());
    }

    public function getNotification(): Notification
    {
        $setting = Setting::where('slug', 'notification')->first();
        if (is_null($setting)) $setting = $this->createNotification();
        return Notification::create($setting->getData());
    }

    public function getImage(): Image
    {
        $setting = Setting::where('slug', 'image')->first();
        if (is_null($setting)) $setting = $this->createImage();
        return Image::create($setting->getData());
    }

    private function createCommon(): Setting
    {
        $setting = Setting::register(
            'Общие настройки',
            'common',
            'Общие настройки торговой компании, товарный учет, время резерва товара и т.п.');

        $setting->data = new Common([]);
        $setting->save();
        return $setting;
    }

    private function createCoupon(): Setting
    {
        $setting = Setting::register(
            'Скидочные купоны',
            'coupon',
            'Настройка скидочных купонов - время действия, минимальная сумма, подарочные супоны и др.',
        );
        $setting->data = new Coupon([]);
        $setting->save();
        return $setting;
    }

    private function createParser(): Setting
    {
        $setting = Setting::register(
            'Настройки парсера',
            'parser',
            'Стоимость доставки, прокси данные для парсера, коэффициент доставки и др.',
        );
        $setting->data = new Parser([]);
        $setting->save();
        return $setting;
    }

    private function createWeb(): Setting
    {
        $setting = Setting::register(
            'Настройки сайта',
            'web',
            'Общие настройки главных цветов, подвала и шапки сайта, логотип, отображения товаров и др.',
        );
        $setting->data = new Web([]);
        $setting->save();
        return $setting;
    }

    private function createMail(): Setting
    {
        $setting = Setting::register(
            'Настройки почты',
            'mail',
            'Настройка входящей почты - почтовые ящики (+пароли), с которых необходимо собирать почту, Настройки исходящей и системной почты',
        );
        $setting->data = new Mail([]);
        $setting->save();
        return $setting;
    }

    private function createNotification(): Setting
    {
        $setting = Setting::register(
            'Настройки уведомлений',
            'notification',
            'API доступы и ключи к мессенджерам, настройки уведомлений'
        );
        $setting->data = new Notification([]);
        $setting->save();
        return $setting;
    }

    private function createImage(): Setting
    {
        $setting =  Setting::register(
            'Изображения на сайте',
            'image',
            'Водяной знак, обрезка и масштабирование изображений товара'
        );
        $setting->data = new Image([]);
        $setting->save();
        $setting->refresh();
        return $setting;
    }
}
