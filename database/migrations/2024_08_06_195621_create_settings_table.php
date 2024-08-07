<?php

use App\Modules\Setting\Entity\Setting;
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
        Schema::create('settings_', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description');
            $table->json('data');
            $table->timestamps();
        });

        Setting::create([
            'name' => 'Общие настройки',
            'slug' => 'common',
            'description' => 'Общие настройки торговой компании, товарный учет, время резерва товара и т.п.',
        ]);
        Setting::create([
            'name' => 'Настройки парсера',
            'slug' => 'parser',
            'description' => 'Стоимость доставки, прокси данные для парсера, коэффициент доставки и др.',
        ]);
        Setting::create([
            'name' => 'Скидочные купоны',
            'slug' => 'coupon',
            'description' => 'Настройка скидочных купонов - время действия, минимальная сумма, подарочные супоны и др.',
        ]);
        Setting::create([
            'name' => 'Настройки сайта',
            'slug' => 'web',
            'description' => 'Общие настройки главных цветов, подвала и шапки сайта, логотип, отображения товаров и др.',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_');
    }
};
