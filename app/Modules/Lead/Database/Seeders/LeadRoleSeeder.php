<?php

namespace App\Modules\Lead\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class LeadRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('lead', 'Работа с лидами');
        $lead = $this->fillArrayPermissions('lead', 'lead', $this->listPermissions(false, true));
        $this->createPermission($lead);
        $this->setPermissions('lead', $lead);

        $this->adminSet();
    }

}
