<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Product\Entity\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()->count(10)->create()->each(function (Category $category) {
            $counts = [0, random_int(3, 7)];
            $category->children()->saveMany(Category::factory()->count($counts[array_rand($counts)])->create()
                ->each(function (Category $category) {
                    $counts = [0, random_int(3, 7)];
                    $category->children()->saveMany(Category::factory()->count($counts[array_rand($counts)])->create());
            }));
        });
    }
}
