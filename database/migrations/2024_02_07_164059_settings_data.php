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
        SettingItem::create([
            'setting_id' => $setting->id,
            'tab' => 'common',
            'name' => 'Товарный учет',
            'description' => 'Поступление товаров только через приходные документы',
            'key' => 'accounting',
            'value' => true,
            'type' => SettingItem::KEY_BOOL,
            'sort' => 11,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting = Setting::get('shop');
        SettingItem::where('setting_id', $setting->id)->where('key', 'accounting')->delete();
    }
};
