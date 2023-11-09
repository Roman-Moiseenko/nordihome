<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Entity\Photo;
use App\Modules\Product\Entity\Group;
use Illuminate\Http\Request;

class GroupService
{
    public function create(Request $request): Group
    {
        $group = Group::register($request['name'], $request['description']);

        $this->photo($group, $request->file('file'));
/*
        foreach ($request['products'] as $product_id) {
            $group->products()->attach($product_id);
        }

        $group->push();*/
        return $group;
    }

    public function add_product(Request $request, Group $group)
    {
        $group->products()->attach((int)$request['product_id']);
    }

    public function del_product(Request $request, Group $group)
    {
        $group->products()->detach((int)$request['product_id']);
    }

    public function update(Request $request, Group $group): Group
    {
        $group->update([
            'name' => $request['name'],
            'description' => $request['description'],
        ]);
        $this->photo($group, $request->file('file'));

      /*  $group->products()->detach();

        foreach ($request['products'] as $product_id) {
            $group->products()->attach((int)$product_id);
        }
        $group->push();*/
        return $group;
    }

    public function delete(Group $group)
    {
        $group->products()->detach();
        Group::destroy($group->id);
    }

    public function photo(Group $group, $file): void
    {
        if (empty($file)) return;
        if (!empty($group->photo)) {
            $group->photo->newUploadFile($file);
        } else {
            $group->photo()->save(Photo::upload($file));
        }
        $group->refresh();
    }
}
