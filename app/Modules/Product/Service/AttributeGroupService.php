<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\AttributeGroup;
use Illuminate\Http\Request;

class AttributeGroupService
{
    public function create(Request $request): void
    {
        if (empty($request['name']))
            throw new \DomainException('Незаполненно название группы',);
        AttributeGroup::register($request['name']);
    }

    public function update(Request $request, AttributeGroup $group): void
    {
        if (empty($request['name']))
            throw new \DomainException('Название группы не должно быть пустым');
        $group->update([
            'name' => $request['name']
        ]);
    }

    public function delete(AttributeGroup $group): void
    {
        if ($group->attributes()->count() > 0)
            throw new \DomainException('Нельзя удалить группу с атрибутами');
        AttributeGroup::destroy($group->id);
    }

    public function up(AttributeGroup $group): void
    {
        $groups = AttributeGroup::orderBy('sort')->get();
        $count = count($groups);
        for ($i = 1; $i < $count; $i++) {
            if ($group->isId($groups[$i]->id)) {
                $prev = $groups[$i - 1]->sort;
                $next = $group->sort;
                $group->update(['sort' => $prev]);
                $groups[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function down(AttributeGroup $group): void
    {
        $groups = AttributeGroup::orderBy('sort')->get();
        $count = count($groups);
        for ($i = 0; $i < $count - 1; $i++) {
            if ($group->isId($groups[$i]->id)) {
                $prev = $groups[$i + 1]->sort;
                $next = $group->sort;
                $group->update(['sort' => $prev]);
                $groups[$i + 1]->update(['sort' => $next]);
            }
        }
    }
}
