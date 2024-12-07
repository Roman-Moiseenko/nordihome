<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Group;

class AttributeGroupRepository
{
    public function getById(int $id): AttributeGroup
    {
        return AttributeGroup::Find($id);
    }

    public function byName(string $name): AttributeGroup
    {
        return AttributeGroup::where('name', '=', $name)->first();
    }

    public function get(string $order_by)
    {
        $query = AttributeGroup::orderBy($order_by);

        return $query->get()->map(function (AttributeGroup $group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'quantity' => $group->attributes()->count(),
            ];
        });
        //return AttributeGroup::orderBy($order_by)->get();
    }

}
