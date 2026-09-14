<?php

namespace App\Modules\Setting\Application\Actions;

use App\Modules\Setting\Entity\Setting;
use App\Modules\Setting\Entity\Web;

class GetWebSettingsUseCase
{

    public function execute(): Web
    {
        $setting = Setting::where('slug', 'web')->first();
        if (is_null($setting)) $setting = $this->createWeb();
        return Web::create($setting->getData());
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
}
