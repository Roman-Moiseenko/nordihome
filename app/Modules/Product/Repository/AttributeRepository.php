<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;

class AttributeRepository
{

    public function existAndGet(int $id):? Attribute
    {
        try {
            return Attribute::findOrFail($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

}
