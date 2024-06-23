<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Entity\Photo;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class GroupService
{
    public function create(Request $request): Group
    {
        $group = Group::register($request['name'], $request['description'] ?? '');
        $this->photo($group, $request->file('file'));

        return $group;
    }

    public function add_product(Group $group, int $product_id)
    {
        if (!$group->isProduct($product_id))
            $group->products()->attach($product_id);
    }

    public function add_products(Group $group, string $textarea)
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add_product($group, $product->id);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
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

    /*
    public function setPriceFromPromotion(int $group_id, ?int $discount)
    {
        $group = Group::find($group_id);
        foreach ($group->products as $product) {
            $new_price = is_null($discount) ? null : (int)ceil($product->getLastPrice() * (1 - $discount / 100));
            $group->products()->updateExistingPivot($product->id, ['price' => $new_price]);
        }
    }
    */
}
