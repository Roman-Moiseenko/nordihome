<?php

namespace App\Modules\Accounting\Database\Seeders;

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
        $pricingRule = $this->fillArrayPermissions('accounting', 'pricing', $this->listPermissions(false, false));
        $organization = $this->fillArrayPermissions('accounting', 'organization', $this->listPermissions(false, false));
        $trader = $this->fillArrayPermissions('accounting', 'trader', $this->listPermissions(false, false));
        $this->createPermission($stock);
        $this->createPermission($price);
        $this->createPermission($pricingRule);
        $this->createPermission($organization);
        $this->createPermission($trader);
        $this->setPermissions('accounting', $stock);
        $this->setPermissions('accounting', $price);
        $this->setPermissions('accounting', $pricingRule);
        $this->setPermissions('accounting', $organization);
        $this->setPermissions('accounting', $trader);

        $this->adminSet();
    }

}
