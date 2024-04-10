<?php

use App\Modules\Order\Entity\Order\Order;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable();
        });
        /** @var Order[] $orders */
        $orders = Order::get();
        foreach ($orders as $order) {
            if (!empty($manager = $order->getManager())) {
                $order->manager_id = $manager->id;
                $order->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('manager_id');
        });
    }
};
