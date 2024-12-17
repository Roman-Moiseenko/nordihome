<?php

use App\Modules\Setting\Entity\Setting;
use Illuminate\Database\Migrations\Migration;
use App\Modules\Setting\Entity\Mail;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Setting::create([
            'name' => 'Настройки почты',
            'slug' => 'mail',
            'description' => 'Настройка входящей почты - почтовые ящики (+пароли), с которых необходимо собирать почту, Настройки исходящей и системной почты',
            'class' => Mail::class,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('slug', 'mail')->first()->delete();
    }
};
