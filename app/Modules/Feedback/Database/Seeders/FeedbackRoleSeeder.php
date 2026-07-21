<?php

namespace App\Modules\Feedback\Database\Seeders;

use App\Modules\Auth\Domain\ValueObjects\RoleName;
use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class FeedbackRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //Системные роли и доступы текущего модуля

        $this->addRole('feedback', 'Обратная связь');
        $form = $this->fillArrayPermissions('feedback', 'form', $this->listPermissions(false, true));
        $this->createPermission($form);
        $this->setPermissions('feedback', $form);
        $this->adminSet();
    }

}
