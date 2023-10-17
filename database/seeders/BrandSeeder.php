<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Product\Entity\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        for($i = 0; $i < 10; $i++) {
            $faker = \Faker\Factory::create('ru_RU');
            $user = Brand::register(
                $faker->unique()->company(),
                $faker->text(),
                $faker->url(),
                [
                    $faker->url(),
                    $faker->url(),
                ]
            );
            $user->save();
        }
    }
}
