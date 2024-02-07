<?php

use App\Entity\Admin;
use App\Entity\FullName;
use App\Modules\Delivery\Entity\UserDelivery;
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
