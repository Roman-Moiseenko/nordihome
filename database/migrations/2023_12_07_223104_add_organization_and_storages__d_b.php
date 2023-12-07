<?php

use App\Http\Controllers\Admin\Accounting\Organization;
use App\Http\Controllers\Admin\Accounting\Storage;
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
        $organization = Organization::register('НОРДИХОУМ');
        $stor_1 = Storage::register($organization->id, 'На Советском', true);
        $stor_1->setDelivery('', 'Калининград, ул. Советский проспект 103А корпус 1');
        $stor_2 = Storage::register($organization->id, 'На Батальной', true);
        $stor_2->setDelivery('', 'Калининград, ул. Батальная 18, 2 этаж');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
