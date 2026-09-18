<?php

namespace App\Modules\Accounting\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class AccountingRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('accounting', 'Работа с товарным учетом');
        $stock = $this->fillArrayPermissions('accounting', 'stock', $this->listPermissions(false, true));
        $price = $this->fillArrayPermissions('accounting', 'price', $this->listPermissions(true, true));
        $this->createPermission($stock);
        $this->createPermission($price);
        $this->setPermissions('accounting', $stock);
        $this->setPermissions('accounting', $price);

        $this->adminSet();
    }

}
