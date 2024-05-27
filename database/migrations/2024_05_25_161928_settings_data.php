<?php

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use App\Modules\Admin\Service\SettingService;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon_first_time')->update(['description' => 'Сколько действует первый купон на покупку (в днях)']);

        SettingService::group(
            [
                'setting_id' => $setting->id,
                'tab' => 'coupons',
            ],
            [
                [
                    'name' => 'Бонус за отзыв',
                    'description' => 'Включить бонусный купон за каждый отзыв при покупке',
                    'key' => 'bonus_review',
                    'value' => true,
                    'type' => SettingItem::KEY_BOOL,
                    'sort' => 23,
                ],
                [
                    'name' => 'Сумма бонуса за отзыв',
                    'description' => 'Награждение в рублях за каждый отзыв',
                    'key' => 'bonus_amount',
                    'value' => 100,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 24,
                ],
                [
                    'name' => 'Задержка на отзыв',
                    'description' => 'Время отправления запроса на отзыв после завершения заказа (в днях)',
                    'key' => 'bonus_discount_delay',
                    'value' => 3,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 25,
                ],
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon_first_time')->update(['description' => 'Сколько действует первый купон на покупку (в часах)']);

        SettingItem::where('setting_id', $setting->id)->where('key', 'bonus_review')->delete();
        SettingItem::where('setting_id', $setting->id)->where('key', 'bonus_amount')->delete();
        SettingItem::where('setting_id', $setting->id)->where('key', 'bonus_discount_delay')->delete();
    }
};
