<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeders = [];

        // Собираем сидеры, зарегистрированные модулями через seed.handler
        if (app()->bound('seed.handler')) {
            $handler = app('seed.handler');
            $seeders = $handler->getSeeders();
        }

        $this->call($seeders);
    }
}
