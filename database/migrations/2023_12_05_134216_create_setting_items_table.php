<?php

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
        Schema::create('setting_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('settings')->onDelete('cascade');
            $table->string('name')->default('');
            $table->string('description')->default('');
            $table->string('tab')->default('common');
            $table->string('key');
            $table->json('value');
            $table->integer('type')->default(SettingItem::KEY_STRING);
            $table->unique(['setting_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('setting_items');
    }
};
