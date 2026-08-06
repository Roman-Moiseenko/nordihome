<?php

namespace App\Modules\Order\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class OrderRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('order', 'Работа с заказом');
        $category = $this->fillArrayPermissions('order', 'order', $this->listPermissions(false, true));
        $expense = $this->fillArrayPermissions('order', 'expense', $this->listPermissions(false, false));
        $payment = $this->fillArrayPermissions('order', 'payment', $this->listPermissions(false, false));
        $this->createPermission($category);
        $this->createPermission($expense);
        $this->createPermission($payment);
        $this->setPermissions('order', $category);
        $this->setPermissions('order', $expense);
        $this->setPermissions('order', $payment);

        $this->adminSet();
    }

}
