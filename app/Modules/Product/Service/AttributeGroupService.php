<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\AttributeGroup;
use Illuminate\Http\Request;

class AttributeGroupService
{
    public function create(Request $request)
    {
        if (empty($request['name'])) {
            flash('Незаполненно название группы', 'danger');
        } else {
            AttributeGroup::register($request['name']);
        }
    }

    public function update(Request $request, AttributeGroup $group)
    {
        if (empty($request['name'])) {
            flash('Название группы не должно быть пустым', 'danger');
        } else {
            $group->update([
                'name' => $request['name']
            ]);
        }
    }

    public function delete(AttributeGroup $group)
    {
        if (count($group->attributes) > 0) {
            flash('Нельзя удалить группу с атрибутами', 'danger');
        } else {
            AttributeGroup::destroy($group->id);
        }
    }

    public function up(AttributeGroup $group)
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

    public function down(AttributeGroup $group)
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
