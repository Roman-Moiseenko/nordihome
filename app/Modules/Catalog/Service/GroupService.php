<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Service;

use App\Modules\Catalog\Entity\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupService
{
    public function create(Request $request): Group
    {
        return Group::register(
            name: $request->string('name')->trim()->value()
        );
    }

    public function addProduct(Group $group, int $product_id): void
    {
        if ($group->isProduct($product_id)) throw new \DomainException('Товар уже добавлен в группу');
            $group->products()->attach($product_id);
    }

    public function addProducts(Group $group, mixed $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($group,
                $product['product_id'],
            );
        }
    }

    public function del_product(Request $request, Group $group): void
    {
        $group->products()->detach($request->integer('product_id'));
    }


    public function delete(Group $group): void
    {
        $group->products()->detach();
        Group::destroy($group->id);
    }

    public function publishedById(int $data_id): void
    {
        /** @var Group $group */
        $group = Group::find($data_id);
        $group->published = true;
        $group->save();
    }

    public function setInfo(Group $group, Request $request): Group
    {
        $slug = $request->string('slug')->trim()->value();
        $name = $request->string('name')->trim()->value();

        $group->update([
            'name' => $name,
            'description' => $request->string('description')->trim()->value(),
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'published' => $request->boolean('published')
        ]);
        $group->saveImage($request->file('file'), $request->boolean('clear_file'));
        return $group;
    }

}
