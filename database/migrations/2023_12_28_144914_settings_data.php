<?php

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
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
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_order')->update([
            'key' => 'reserve_making',
        ]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_preorder')->update([
            'key' => 'reserve_order',
            'description' => 'Время резервирование сформированного заказа для оплаты, в минутах',

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_order')->update([
            'key' => 'reserve_preorder',
            'description' => 'Время резервирования товара на вывоз, при оплате на месте, в минутах',
        ]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_making')->update([
            'key' => 'reserve_order',
        ]);
    }
};
