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
        Setting::create([
            'name' => 'Настройки уведомлений',
            'slug' => 'notification',
            'description' => 'API доступы и ключи к мессенджерам, настройки уведомлений',
            'class' => Notification::class,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('slug', 'notification')->first()->delete();
    }
};
