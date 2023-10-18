<?php
declare(strict_types=1);

namespace Database\Factories\Modules\Product\Entity;

use App\Modules\Product\Entity\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{

    protected $model = Category::class;
    public function definition()
    {
        $faker = \Faker\Factory::create('ru_RU');
        return [
            'name' => $faker->unique()->name,
            'slug' => $faker->unique()->slug(2),
            'parent_id' => null,
            'title' => '',
            'description' => '',
        ];
    }
}
