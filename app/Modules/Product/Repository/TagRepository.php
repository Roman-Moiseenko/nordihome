<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Tag;

class TagRepository
{
    public function exists(mixed $id): bool
    {
        return !is_null(Tag::find($id));
    }
}
