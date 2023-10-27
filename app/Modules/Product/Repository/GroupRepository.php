<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\AttributeGroup;

class GroupRepository
{
    public function getById(int $id): AttributeGroup
    {
        return AttributeGroup::Find($id);
    }
}
