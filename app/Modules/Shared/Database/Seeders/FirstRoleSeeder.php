<?php

namespace App\Modules\Shared\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class FirstRoleSeeder extends Seeder
{
    use RoleSeeder;

    public function run(): void
    {
        //Базовые роли, доступы не назначаются
        $this->addRole(RoleName::ADMIN);
        $this->addRole(RoleName::CLIENT);
        $this->addRole(RoleName::STAFF);
    }
}
