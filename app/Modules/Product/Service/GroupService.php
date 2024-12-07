<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;

class GroupService
{
    public function create(Request $request): Group
    {
        return Group::register(
            name: $request->string('name')->trim()->value()
        );
    }

    public function add_product(Group $group, int $product_id): void
    {
        if ($group->isProduct($product_id)) throw new \DomainException('Товар уже добавлен в группу');
            $group->products()->attach($product_id);
    }

    public function add_products(Group $group, string $textarea): void
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add_product($group, $product->id);
            } else {
                throw new \DomainException('Товар с артикулом ' . $item . ' не найден');
            }
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
