<?php

namespace App\Modules\Marketplace\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class MarketplaceRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('marketplace', 'Работа с маркетплейсами');
        $avito = $this->fillArrayPermissions('marketplace', 'avito', $this->listPermissions(false, true));

        $this->createPermission($avito);

        $this->setPermissions('marketplace', $avito);

        $this->adminSet();
    }

}
