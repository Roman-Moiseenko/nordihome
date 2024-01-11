<?php

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use App\Modules\Admin\Service\SettingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon')->update([
            'tab' => 'coupons', 'sort' => 20,
        ]);

        SettingService::group(
            [
                'setting_id' => $setting->id,
                'tab' => 'coupons',
            ],
            [
                [
                    'name' => 'Скидка при регистрации',
                    'description' => 'Сумма в рублях на первую скидку',
                    'key' => 'coupon_first_bonus',
                    'value' => 500,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 21,
                ],
                [
                    'name' => 'Время скидки при регистрации',
                    'description' => 'Сколько действует первый купон на покупку (в часах)',
                    'key' => 'coupon_first_time',
                    'value' => 3,
                    'type' => SettingItem::KEY_INTEGER,
                    'sort' => 22,
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
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon')->update([
            'tab' => 'common',
        ]);

        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon_first_bonus')->delete();
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon_first_time')->delete();

    }
};
