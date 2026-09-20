<?php

namespace App\Modules\Output\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class OutputRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('output', 'Работа с выгрузками');
        $feed = $this->fillArrayPermissions('output', 'feed', $this->listPermissions(false, true));
        $this->createPermission($feed);

        $this->setPermissions('output', $feed);


        $this->adminSet();
    }

}
