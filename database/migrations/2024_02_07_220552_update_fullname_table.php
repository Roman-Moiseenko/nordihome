<?php

use App\Entity\FullName;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Delivery\Entity\UserDelivery;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Admin::get() as $item) {
            $item->update([
                'fullname' => new FullName(),
            ]);
        }
        foreach (UserDelivery::get() as $item) {
            $item->update([
                'fullname' => new FullName(),
            ]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
