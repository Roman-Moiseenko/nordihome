<?php

namespace App\Modules\Catalog\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class CatalogRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('catalog', 'Работа с каталогом');
        $category = $this->fillArrayPermissions('catalog', 'category', $this->listPermissions(false, true));
        $product = $this->fillArrayPermissions('catalog', 'product', $this->listPermissions(true, true));
        $other = $this->fillArrayPermissions('catalog', 'other', $this->listPermissions(false, false));
        $this->createPermission($category);
        $this->createPermission($product);
        $this->createPermission($other);
        $this->setPermissions('catalog', $category);
        $this->setPermissions('catalog', $product);
        $this->setPermissions('catalog', $other);

        $this->adminSet();
    }

}
