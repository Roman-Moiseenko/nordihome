<?php

use App\Modules\Order\Entity\Order\OrderAddition;
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
        Schema::table('order_additions', function (Blueprint $table) {
            $table->timestamps();
        });
        OrderAddition::get()->each(function (OrderAddition $item) {
            $item->created_at = now();
            $item->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_additions', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
