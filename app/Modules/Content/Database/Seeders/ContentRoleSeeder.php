<?php

namespace App\Modules\Content\Database\Seeders;

use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class ContentRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addRole('content', 'Работа с контентом');
        $page = $this->fillArrayPermissions('content', 'page', $this->listPermissions(true, true));
        $seo = $this->fillArrayPermissions('content', 'seo', $this->listPermissions(false, false));
        $widget = $this->fillArrayPermissions('content', 'widget', $this->listPermissions(false, false));
        $this->createPermission($page);
        $this->createPermission($seo);
        $this->createPermission($widget);
        $this->setPermissions('content', $page);
        $this->setPermissions('content', $seo);
        $this->setPermissions('content', $widget);

        $this->adminSet();
    }
}
