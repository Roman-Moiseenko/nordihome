<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository
{
    public function exists(int $id): bool
    {

        try {
            Category::findOrFail($id);
        } catch (\Throwable $e) {
            return false;
        }
        return true;
    }
}
