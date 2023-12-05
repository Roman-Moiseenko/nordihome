<?php

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use App\Modules\Admin\Service\SettingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $setting = Setting::register('Магазин', 'shop');

        SettingService::group(
            [
                'setting_id' => $setting->id,
                'tab' => 'common',
            ],
            [
                [
                    'name' => 'Предзаказ',
                    'description' => 'Возможность оформлять предзаказ, когда товара нет в наличии',
                    'key' => 'pre_order',
                    'value' => false,
                    'type' => SettingItem::KEY_BOOL,
                ],
                [
                    'name' => 'Пагинация товаров',
                    'description' => 'Количество товаров на странице',
                    'key' => 'paginate',
                    'value' => 20,
                    'type' => SettingItem::KEY_INTEGER,
                ],
                [
                    'name' => 'Резерв товара',
                    'description' => 'Время резервирования товара в корзине, в минутах',
                    'key' => 'reserve_cart',
                    'value' => 60,
                    'type' => SettingItem::KEY_INTEGER,
                ],

                [
                    'name' => 'Только оффлайн',
                    'description' => 'Продажа товаров только оффлайн, ИМ недоступен',
                    'key' => 'only_offline',
                    'value' => true,
                    'type' => SettingItem::KEY_BOOL,
                ],

                [
                    'name' => 'Доставка по региону',
                    'description' => 'Осуществляется ли доставка товаров по региону собственными силами.',
                    'key' => 'delivery_local',
                    'value' => true,
                    'type' => SettingItem::KEY_BOOL,
                ],
                [
                    'name' => 'Доставка ТК',
                    'description' => 'Осуществляется ли доставка товара Транспортными компаниями',
                    'key' => 'delivery_all',
                    'value' => true,
                    'type' => SettingItem::KEY_BOOL,
                ],

                [
                    'name' => 'Логотип',
                    'description' => 'Логотип для сайта, с прозрачным фоном (svg, png)',
                    'key' => 'logo_img',
                    'value' => '/images/logo-nordi-home-2.svg',
                    'type' => SettingItem::KEY_STRING,
                ],
                [
                    'name' => 'Описание логотипа',
                    'description' => 'Подпись (alt) под логотипом - Бренд, магазин или компания',
                    'key' => 'logo_alt',
                    'value' => 'NORDI Home',
                    'type' => SettingItem::KEY_STRING,
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
