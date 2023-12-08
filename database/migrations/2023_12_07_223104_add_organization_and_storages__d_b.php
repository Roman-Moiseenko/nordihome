<?php

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Storage;
use Illuminate\Database\Migrations\Migration;

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

        Storage::where('name', 'На Советском')->delete();
        Storage::where('name', 'На Батальной')->delete();
        Organization::where('name', 'НОРДИХОУМ')->delete();
    }
};
