<?php

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use App\Modules\Admin\Service\SettingService;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = Setting::get('shop');
        SettingService::group(
            [
                'setting_id' => $setting->id,
                'tab' => 'parser',
            ],
            [
                [
                    'name' => 'Курс злота',
                    'description' => 'Внутренний курс - коэффициент наценки на стоимость',
                    'key' => 'parser_coefficient',
                    'value' => 22,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 31,
                ],
                [
                    'name' => 'Доставка',
                    'description' => 'Минимальная стоимость доставки',
                    'key' => 'parser_delivery',
                    'value' => 1000,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 32,
                ],
            ],
        );
        foreach (ParserService::DELIVERY_PERIOD as $i => $item) {
            SettingItem::create([
                'setting_id' => $setting->id,
                'tab' => 'parser',
                'name' => 'Доставка за 1 кг',
                'description' => 'Стоимость доставки за 1 кг при весе от ' . $item['min'] . ' до ' . $item['max'],
                'key' => $item['slug'],
                'value' => $item['value'],
                'type' => SettingItem::KEY_INTEGER,
                'sort' => (int)(33 + $i),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'parser_coefficient')->delete();
        SettingItem::where('setting_id', $setting->id)->where('key', 'parser_delivery')->delete();
        foreach (ParserService::DELIVERY_PERIOD as $item) {
            SettingItem::where('setting_id', $setting->id)->where('key', $item['slug'])->delete();
        }
    }
};
