<?php

namespace Database\Seeders;

use App\Modules\Shared\Database\Seeders\FirstRoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeders = [];
        $this->call([
            FirstRoleSeeder::class,
        ]);
        // Собираем сидеры, зарегистрированные модулями через seed.handler
        if (app()->bound('seed.handler')) {
            $handler = app('seed.handler');
            $seeders = $handler->getSeeders();
        }

        $this->call($seeders);
    }
}
