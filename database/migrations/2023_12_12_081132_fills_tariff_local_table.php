<?php

use App\Modules\Delivery\Entity\Local\Tariff;
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

        Tariff::register(6, 500);
        Tariff::register(12, 700);
        Tariff::register(20, 1000);
        Tariff::register(30, 1100);
        Tariff::register(40, 1500);
        Tariff::register(80, 2000);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
