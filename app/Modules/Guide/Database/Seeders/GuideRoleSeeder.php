<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class GuideRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('guide', 'Работа с каталогом');
        $guide = $this->fillArrayPermissions('guide', 'guide', $this->listPermissions(false, true));
  //      $product = $this->fillArrayPermissions('guide', 'product', $this->listPermissions(true, true));
    //    $other = $this->fillArrayPermissions('guide', 'other', $this->listPermissions(false, false));
        $this->createPermission($guide);
//        $this->createPermission($product);
//        $this->createPermission($other);
        $this->setPermissions('guide', $guide);
//        $this->setPermissions('catalog', $product);
    //    $this->setPermissions('catalog', $other);

        $this->adminSet();
    }

}
