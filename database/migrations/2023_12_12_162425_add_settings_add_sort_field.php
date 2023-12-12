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
        Schema::table('setting_items', function (Blueprint $table) {
            $table->integer('sort')->default(0);
        });

        $setting = Setting::get('shop');

        SettingItem::create([
            'setting_id' => $setting->id,
            'tab' => 'common',
            'type' => SettingItem::KEY_INTEGER,
            'name' => 'Скидка по купонам',
            'description' => 'Максимальная скидка в %% от сумы заказа',
            'key' => 'coupon',
            'value' => 30,
            'sort' => 0,
        ]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'pre_order')->update(['sort' => 2]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'paginate')->update(['sort'=> 9]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'only_offline')->update(['sort' => 2]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'delivery_local')->update(['sort' => 2]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'delivery_all')->update(['sort' => 2 ]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'logo_img')->update(['sort' => 10]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'logo_alt')->update(['sort' => 10]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_cart')->update(['sort' => 1]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_order')->update(['sort' => 1]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_preorder')->update(['sort' => 1]);
        SettingItem::where('setting_id', $setting->id)->where('key', 'reserve_shop')->update(['sort' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_items', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'coupon')->delete();
    }
};
