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
                'tab' => 'common',
                'type' => SettingItem::KEY_INTEGER,
            ],
            [
                [
                    'name' => 'Резерв товара (Заказ)',
                    'description' => 'Время резервирования товара при оформление заказа, в минутах',
                    'key' => 'reserve_order',
                    'value' => 60,
                ],
                [
                    'name' => 'Резерв товара (Самовывоз)',
                    'description' => 'Время резервирования товара на вывоз, при оплате на месте, в минутах',
                    'key' => 'reserve_preorder',
                    'value' => 24 * 60,
                ],
                [
                    'name' => 'Резерв товара (Касса)',
                    'description' => 'Время резервирования товара при покупке в магазине, в минутах',
                    'key' => 'reserve_shop',
                    'value' => 25,
                ],
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
