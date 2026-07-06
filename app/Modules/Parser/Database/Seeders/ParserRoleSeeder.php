<?php

namespace App\Modules\Parser\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class ParserRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('parser', 'Работа с парсером');
        $category = $this->fillArrayPermissions('parser', 'category', $this->listPermissions(false, true));
        $product = $this->fillArrayPermissions('parser', 'product', $this->listPermissions(true, true));
        $other = $this->fillArrayPermissions('parser', 'other', $this->listPermissions(false, false));
        $this->createPermission($category);
        $this->createPermission($product);
        $this->createPermission($other);
        $this->setPermissions('parser', $category);
        $this->setPermissions('parser', $product);
        $this->setPermissions('parser', $other);

        $this->adminSet();
    }

}
