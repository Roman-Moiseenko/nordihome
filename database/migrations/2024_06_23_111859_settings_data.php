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
        SettingService::group(
            [
                'setting_id' => $setting->id,
                'tab' => 'parser',
            ],
            [
                [
                    'name' => 'Адрес прокси-сервера',
                    'description' => 'Формат записи ip:port, например, 195.20.0.20:8080',
                    'key' => 'proxy_ip',
                    'value' => '',
                    'type' => SettingItem::KEY_STRING,
                    'sort' => 50,
                ],
                [
                    'name' => 'Доступ к прокси-серверу',
                    'description' => 'Формат записи логин:пароль,  например, user111:p@Sw0rD',
                    'key' => 'proxy_user',
                    'value' => '',
                    'type' => SettingItem::KEY_STRING,
                    'sort' => 51,
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
        SettingItem::where('setting_id', $setting->id)->where('key', 'proxy_ip')->delete();
        SettingItem::where('setting_id', $setting->id)->where('key', 'proxy_user')->delete();
    }
};
