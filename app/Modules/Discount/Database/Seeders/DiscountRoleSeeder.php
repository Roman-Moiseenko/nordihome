<?php

namespace App\Modules\Discount\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class DiscountRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('discount', 'Работа со скидками');
        $promotion = $this->fillArrayPermissions('discount', 'promotion', $this->listPermissions(false, true));
        $discount = $this->fillArrayPermissions('discount', 'discount', $this->listPermissions(true, true));
        $coupon = $this->fillArrayPermissions('discount', 'coupon', $this->listPermissions(false, false));
        $this->createPermission($promotion);
        $this->createPermission($discount);
        $this->createPermission($coupon);
        $this->setPermissions('discount', $promotion);
        $this->setPermissions('discount', $discount);
        $this->setPermissions('discount', $coupon);

        $this->adminSet();
    }

}
