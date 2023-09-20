<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Entity\User\FullName;
use App\Entity\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //User::factory()->count(10)->create();


        for($i = 0; $i < 100; $i++) {
            $faker = \Faker\Factory::create('ru_RU');
            $active = $faker->boolean;
            $user = User::new($faker->unique()->safeEmail(), $faker->phoneNumber());
            $user->setFullName(new FullName(
                $faker->lastName(),
                $faker->firstName(),
                $faker->middleName()
            ));
            $user->save();
        }
    }
}
