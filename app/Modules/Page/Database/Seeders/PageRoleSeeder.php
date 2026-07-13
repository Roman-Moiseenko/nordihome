<?php

namespace App\Modules\Page\Database\Seeders;

use App\Modules\Shared\Infrastructure\Persistence\RoleSeeder;
use Illuminate\Database\Seeder;

class PageRoleSeeder extends Seeder
{
    use RoleSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addRole('website', 'Работа с сайтом');
        $page = $this->fillArrayPermissions('website', 'page', $this->listPermissions(true, true));
        $seo = $this->fillArrayPermissions('website', 'seo', $this->listPermissions(true, true));
        $this->createPermission($page);
        $this->createPermission($seo);
        $this->setPermissions('website', $page);
        $this->setPermissions('website', $seo);

        $this->adminSet();
    }
}
